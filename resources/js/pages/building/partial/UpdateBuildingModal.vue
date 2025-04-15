<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { toast } from 'vue-sonner';
import ConfirmDialog from './ConfirmDialog.vue';

const props = defineProps<{
    show: boolean;
    building?: {
        id: string;
        name: string;
        building_type: string;
        state: string;
    } | null;
}>();

const emit = defineEmits<{
    'update:show': [value: boolean];
}>();

const showConfirmDialog = ref(false);

const buildingTypes = [
    { value: 'Resedential', label: 'Resedential' },
    { value: 'Commercial', label: 'Commercial' },
];

const states = [
    { value: 'Johor', label: 'Johor' },
    { value: 'Kedah', label: 'Kedah' },
    { value: 'Kelantan', label: 'Kelantan' },
    { value: 'Melaka', label: 'Melaka' },
    { value: 'Negeri Sembilan', label: 'Negeri Sembilan' },
    { value: 'Pahang', label: 'Pahang' },
    { value: 'Perak', label: 'Perak' },
    { value: 'Perlis', label: 'Perlis' },
    { value: 'Pulau Pinang', label: 'Pulau Pinang' },
    { value: 'Sabah', label: 'Sabah' },
    { value: 'Sarawak', label: 'Sarawak' },
    { value: 'Selangor', label: 'Selangor' },
    { value: 'Terengganu', label: 'Terengganu' },
    { value: 'Kuala Lumpur', label: 'Kuala Lumpur' },
    { value: 'Labuan', label: 'Labuan' },
    { value: 'Putrajaya', label: 'Putrajaya' },
];

const form = useForm({
    name: props.building?.name ?? '',
    building_type: props.building?.building_type ?? '',
    state: props.building?.state ?? '',
});

watch(
    () => props.building,
    (newBuilding) => {
        if (newBuilding) {
            form.name = newBuilding.name;
            form.building_type = newBuilding.building_type;
            form.state = newBuilding.state;
        }
    },
    { immediate: true },
);

const onSubmit = () => {
    showConfirmDialog.value = true;
};

const handleConfirmedSubmit = () => {
    if (!props.building?.id) return;

    form.put(route('buildings.update', props.building.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast({
                title: 'Success',
                description: 'Building updated successfully',
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

const onClose = () => {
    emit('update:show', false);
    form.reset();
};
</script>

<template>
    <ConfirmDialog :open="showConfirmDialog" @update:open="showConfirmDialog = $event" @confirm="handleConfirmedSubmit" />
    <Dialog :open="show" @update:open="onClose">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Update Building</DialogTitle>
            </DialogHeader>

            <form @submit.prevent="onSubmit" class="space-y-4">
                <div class="space-y-2">
                    <label class="block text-sm font-medium">Name</label>
                    <Input v-model="form.name" placeholder="Enter building name" :disabled="form.processing" />
                    <div v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium">Building Type</label>
                    <select v-model="form.building_type" class="w-full rounded border p-2" :disabled="form.processing">
                        <option v-for="type in buildingTypes" :key="type.value" :value="type.value">
                            {{ type.label }}
                        </option>
                    </select>
                    <div v-if="form.errors.building_type" class="text-sm text-red-500">{{ form.errors.building_type }}</div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium">State</label>
                    <select v-model="form.state" class="w-full rounded border p-2" :disabled="form.processing">
                        <option v-for="state in states" :key="state.value" :value="state.value">
                            {{ state.label }}
                        </option>
                    </select>
                    <div v-if="form.errors.state" class="text-sm text-red-500">{{ form.errors.state }}</div>
                </div>

                <DialogFooter>
                    <Button type="button" variant="outline" @click="onClose" :disabled="form.processing"> Cancel </Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Updating...' : 'Update Building' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
