<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
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

const buildingTypes = [
    { value: 'Residential', label: 'Residential' },
    { value: 'Commercial', label: 'Commercial' },
];

const form = useForm({
    month: 1,
    building_id: '',
    building_type: '',
    usability: 0,
});

const onSubmit = () => {
    showConfirmDialog.value = true;
};

const handleConfirmedSubmit = () => {
    form.post(route('bills.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast({
                title: 'Success',
                description: 'Bill created successfully',
                duration: 3000,
            });
            onClose();
        },
        onError: (errors) => {
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
    isSubmitting.value = false;
};
</script>

<template>
    <StoreBillConfirmDialog :open="showConfirmDialog" @update:open="showConfirmDialog = $event" @confirm="handleConfirmedSubmit" />
    <Dialog :open="show" @update:open="onClose">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Create New Bill</DialogTitle>
            </DialogHeader>

            <form @submit.prevent="onSubmit" class="space-y-4">
                <div class="space-y-2">
                    <label class="block text-sm font-medium">Month</label>
                    <Select v-model="form.month">
                        <SelectTrigger>
                            <SelectValue placeholder="Select month" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="month in months" :key="month.value" :value="month.value">
                                {{ month.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <div v-if="form.errors.month" class="text-sm text-red-500">{{ form.errors.month }}</div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium">Building Type</label>
                    <Select v-model="form.building_type">
                        <SelectTrigger>
                            <SelectValue placeholder="Select building type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="type in buildingTypes" :key="type.value" :value="type.value">
                                {{ type.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <div v-if="form.errors.building_type" class="text-sm text-red-500">{{ form.errors.building_type }}</div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium">Building</label>
                    <Select v-model="form.building_id">
                        <SelectTrigger>
                            <SelectValue placeholder="Select building" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="building in buildings" :key="building.id" :value="building.id">
                                {{ building.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <div v-if="form.errors.building_id" class="text-sm text-red-500">{{ form.errors.building_id }}</div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium">Usability (kWh)</label>
                    <Input type="number" step="0.01" min="0" placeholder="Enter usability in kWh" v-model="form.usability" />
                    <div v-if="form.errors.usability" class="text-sm text-red-500">{{ form.errors.usability }}</div>
                </div>

                <DialogFooter>
                    <Button type="button" variant="outline" @click="onClose" :disabled="form.processing"> Cancel </Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Creating...' : 'Create Bill' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
