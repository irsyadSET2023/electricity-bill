<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod';

import { useForm } from '@inertiajs/vue3';
import { useForm as useVeeForm } from 'vee-validate';
import { onMounted, ref } from 'vue';
import * as z from 'zod';

import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { toast } from 'vue-sonner';
import StoreBillConfirmDialog from './StoreBillConfirmDialog.vue';
const props = defineProps<{
    show: boolean;
}>();

const emit = defineEmits<{
    'update:show': [value: boolean];
}>();

const showConfirmDialog = ref(false);

const months = [
    { value: 1, label: 'January' },
    { value: 2, label: 'February' },
    { value: 3, label: 'March' },
    { value: 4, label: 'April' },
    { value: 5, label: 'May' },
    { value: 6, label: 'June' },
    { value: 7, label: 'July' },
    { value: 8, label: 'August' },
    { value: 9, label: 'September' },
    { value: 10, label: 'October' },
    { value: 11, label: 'November' },
    { value: 12, label: 'December' },
];

const formSchema = toTypedSchema(
    z.object({
        month: z.number().min(1, 'Month is required'),
        building_id: z.string().min(1, 'Building is required'),
        usability: z
            .number()
            .min(1, 'Usability is required')
            .refine((val) => !isNaN(Number(val)) && Number(val) >= 0, 'Must be a positive number'),
    }),
);

const { handleSubmit, setFieldError, resetField, defineField, setFieldValue } = useVeeForm({
    validationSchema: formSchema,
    initialValues: {
        month: 1,
        building_id: '',
        usability: 0,
    },
});

const form = useForm({
    month: '',
    building_id: '',
    usability: 0,
});

const onSubmit = handleSubmit(() => {
    showConfirmDialog.value = true;
});

const handleConfirmedSubmit = () => {
    // Close the dialog
    showConfirmDialog.value = false;

    form.month = form.month;
    form.building_id = form.building_id;
    form.usability = form.usability;

    // Submit the form
    form.post(route('bills.store'), {
        preserveScroll: true,

        onSuccess: (response) => {
            console.log('Success Response', response);
            if (response?.props?.success) {
                toast({
                    title: 'Success',
                    description: 'Bill created successfully',
                    duration: 3000,
                });
                onClose(); // Close the modal after successful submission
            }
        },
        onError: (errors) => {
            console.log('Errors', errors);
            toast({
                title: 'Error',
                description: 'Please check the form for errors',
                variant: 'destructive',
                duration: 3000,
            });
        },
    });
};

const isSubmitting = ref(false);

const buildings = ref<{ id: string; name: string }[]>([]);

const fetchBuildings = async () => {
    try {
        const response = await fetch('/buildings/simple');
        const data = await response.json();
        buildings.value = data;
    } catch (error) {
        console.error('Error fetching buildings:', error);
    }
};

onMounted(() => {
    fetchBuildings();
});

const onClose = () => {
    emit('update:show', false);
    form.reset();
};
</script>

<template>
    <StoreBillConfirmDialog :open="showConfirmDialog" @confirm="handleConfirmedSubmit" />
    <Dialog :open="show" @update:open="onClose">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Create New Bill</DialogTitle>
            </DialogHeader>

            <form @submit.prevent="onSubmit">
                <div class="grid gap-4 py-4">
                    <FormField v-slot="{ componentField }" name="month">
                        <FormItem>
                            <FormLabel>Month</FormLabel>
                            <Select v-bind="componentField">
                                <FormControl>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select month" />
                                    </SelectTrigger>
                                </FormControl>
                                <SelectContent>
                                    <SelectItem v-for="month in months" :key="month.value" :value="month.value">
                                        {{ month.label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField v-slot="{ componentField }" name="building_id">
                        <FormItem>
                            <FormLabel>Building</FormLabel>
                            <Select v-bind="componentField">
                                <FormControl>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select building" />
                                    </SelectTrigger>
                                </FormControl>
                                <SelectContent>
                                    <SelectItem v-for="building in buildings" :key="building.id" :value="building.id">
                                        {{ building.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField v-slot="{ componentField }" name="usability">
                        <FormItem>
                            <FormLabel>Usability (kWh)</FormLabel>
                            <FormControl>
                                <Input type="number" step="0.01" min="0" placeholder="Enter usability in kWh" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>
                </div>

                <DialogFooter>
                    <Button type="button" variant="outline" @click="onClose" :disabled="isSubmitting"> Cancel </Button>
                    <Button type="submit" :disabled="isSubmitting">
                        {{ isSubmitting ? 'Creating...' : 'Create Bill' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
