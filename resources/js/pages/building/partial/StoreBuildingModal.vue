<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { toast } from 'vue-sonner';

const props = defineProps<{
    show: boolean;
}>();

const emit = defineEmits<{
    'update:show': [value: boolean];
}>();

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

// Add interface for User type
interface User {
    id: number;
    name: string;
}

// Add users ref
const users = ref<User[]>([]);

const form = useForm({
    name: '',
    building_type: '',
    state: '',
    user_id: '', // Add user_id field
});

// Add function to fetch users
const fetchUsers = async () => {
    try {
        const response = await fetch(route('users.simple'));
        users.value = await response.json();
    } catch (error) {
        toast.error('Error', { description: 'Failed to fetch user' });
    }
};

// Call fetchUsers when component mounts
onMounted(() => {
    fetchUsers();
});

const onSubmit = () => {
    form.post(route('buildings.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Success', { description: 'Successfully Creating the Building' });
            onClose();
        },
        onError: (errors) => {
            toast.error('Error', { description: 'Error Creating the Building' });
        },
    });
};

const onClose = () => {
    emit('update:show', false);
    form.reset();
};
</script>

<template>
    <Dialog :open="show" @update:open="onClose">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Create New Building</DialogTitle>
            </DialogHeader>

            <form @submit.prevent="onSubmit" class="space-y-4">
                <div class="space-y-2">
                    <label class="block text-sm font-medium">Building Name</label>
                    <Input type="text" placeholder="Enter building name" v-model="form.name" />
                    <div v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium">Assign User</label>
                    <Select v-model="form.user_id">
                        <SelectTrigger>
                            <SelectValue placeholder="Select user" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="user in users" :key="user.id" :value="user.id.toString()">
                                {{ user.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <div v-if="form.errors.user_id" class="text-sm text-red-500">{{ form.errors.user_id }}</div>
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
                    <label class="block text-sm font-medium">State</label>
                    <Select v-model="form.state">
                        <SelectTrigger>
                            <SelectValue placeholder="Select state" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="state in states" :key="state.value" :value="state.value">
                                {{ state.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <div v-if="form.errors.state" class="text-sm text-red-500">{{ form.errors.state }}</div>
                </div>

                <DialogFooter>
                    <Button type="button" variant="outline" @click="onClose" :disabled="form.processing"> Cancel </Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Creating...' : 'Create Building' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
