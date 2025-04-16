<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';

import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { toast } from 'vue-sonner';
import ConfirmDialog from './ConfirmDialog.vue';

const props = defineProps<{
    show: boolean;
    bill?: {
        id: string;
        month: number;
        building_id: string;
        building_type: string;
        usability: number;
    } | null;
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
    month: props.bill?.month ?? 1,
    building_id: props.bill?.building_id ?? '',
    building_type: props.bill?.building_type ?? '',
    usability: props.bill?.usability ?? 0,
});

watch(
    () => props.bill,
    (newBill) => {
        if (newBill) {
            form.month = newBill.month;
            form.building_id = newBill.building_id;
            form.building_type = newBill.building_type;
            form.usability = newBill.usability;
        }
    },
    { immediate: true },
);

const onSubmit = () => {
    showConfirmDialog.value = true;
};

const handleConfirmedSubmit = () => {
    if (!props.bill?.id) return;

    form.put(route('bills.update', props.bill.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Success', { description: 'Successfully Updating the Bill' });

            onClose();
        },
        onError: (errors) => {
            toast.error('Error', { description: 'Error Updating the Bill' });
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
    <ConfirmDialog :open="showConfirmDialog" @update:open="showConfirmDialog = $event" @confirm="handleConfirmedSubmit" />
    <Dialog :open="show" @update:open="onClose">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Update Bill (Month and Usability only)</DialogTitle>
            </DialogHeader>

            <form @submit.prevent="onSubmit" class="space-y-4">
                <div class="space-y-2">
                    <label class="block text-sm font-medium">Month</label>
                    <select v-model="form.month" class="w-full rounded border p-2" :disabled="form.processing">
                        <option v-for="month in months" :key="month.value" :value="month.value">
                            {{ month.label }}
                        </option>
                    </select>
                    <div v-if="form.errors.month" class="text-sm text-red-500">{{ form.errors.month }}</div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium">Building Type</label>
                    <div class="rounded border bg-gray-100 p-2">
                        {{ form.building_type }}
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium">Building</label>
                    <div class="rounded border bg-gray-100 p-2">
                        {{ buildings.find((b) => b.id === form.building_id)?.name || form.building_id }}
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium">Usability (kWh)</label>
                    <Input type="number" step="0.01" min="0" placeholder="Enter usability in kWh" v-model="form.usability" />
                    <div v-if="form.errors.usability" class="text-sm text-red-500">{{ form.errors.usability }}</div>
                </div>

                <DialogFooter>
                    <Button type="button" variant="outline" @click="onClose" :disabled="form.processing"> Cancel </Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Updating...' : 'Update Bill' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
