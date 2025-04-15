<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod';
import { useForm } from 'vee-validate';
import { onMounted, ref } from 'vue';
import * as z from 'zod';

import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { toast } from 'vue-sonner';

const props = defineProps<{
    show: boolean;
}>();

const emit = defineEmits<{
    'update:show': [value: boolean];
}>();

const months = [
    { value: '1', label: 'January' },
    { value: '2', label: 'February' },
    { value: '3', label: 'March' },
    { value: '4', label: 'April' },
    { value: '5', label: 'May' },
    { value: '6', label: 'June' },
    { value: '7', label: 'July' },
    { value: '8', label: 'August' },
    { value: '9', label: 'September' },
    { value: '10', label: 'October' },
    { value: '11', label: 'November' },
    { value: '12', label: 'December' },
];

const formSchema = toTypedSchema(
    z.object({
        month: z.string().min(1, 'Month is required'),
        building_id: z.string().min(1, 'Building is required'),
        usability: z
            .number()
            .min(1, 'Usability is required')
            .refine((val) => !isNaN(Number(val)) && Number(val) >= 0, 'Must be a positive number'),
    }),
);

const form = useForm({
    validationSchema: formSchema,
    initialValues: {
        month: '',
        building_id: '',
        usability: '',
    },
});

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

console.log(form.errors);

const onSubmit = form.handleSubmit(async (values) => {
    try {
        isSubmitting.value = true;
        await fetch('/bills', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(values),
        });
        emit('update:show', false);
        form.resetForm();
    } catch (error) {
        console.error('Error submitting form:', error);
        toast.error('Error submitting form');
    } finally {
        isSubmitting.value = false;
        toast.success('Bill created successfully');
    }
});

const onClose = () => {
    emit('update:show', false);
    form.resetForm();
};
</script>

<template>
    <Dialog :open="show" @update:open="onClose">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Create New Bill</DialogTitle>
            </DialogHeader>

            <form @submit="onSubmit">
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
