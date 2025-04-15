<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';

interface Props {
    show: boolean;
    buildingType: string;
    month: string;
    state: string;
    buildingTypes: Array<{ value: string; label: string }>;
    months: Array<{ value: string; label: string }>;
    states: Array<{ value: string; label: string }>;
}

const props = defineProps<Props>();
const emit = defineEmits(['update:show', 'update:buildingType', 'update:month', 'update:state', 'apply', 'clear']);

const handleClose = () => {
    emit('update:show', false);
};

const handleClear = () => {
    emit('clear');
    handleClose();
};

const handleApply = () => {
    emit('apply');
    handleClose();
};
</script>

<template>
    <Dialog :open="show" @update:open="handleClose">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Filter Bills</DialogTitle>
            </DialogHeader>
            <div class="grid gap-4 py-4">
                <div class="grid gap-2">
                    <Label>Building Type</Label>
                    <Select v-model="buildingType">
                        <SelectTrigger>
                            <SelectValue placeholder="Select building type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectLabel>Building Types</SelectLabel>
                                <SelectItem v-for="type in buildingTypes" :key="type.value" :value="type.value">
                                    {{ type.label }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
                <div class="grid gap-2">
                    <Label>Month</Label>
                    <Select v-model="month">
                        <SelectTrigger>
                            <SelectValue placeholder="Select month" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectLabel>Months</SelectLabel>
                                <SelectItem v-for="month in months" :key="month.value" :value="month.value">
                                    {{ month.label }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
                <div class="grid gap-2">
                    <Label>State</Label>
                    <Select v-model="state">
                        <SelectTrigger>
                            <SelectValue placeholder="Select state" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectLabel>States</SelectLabel>
                                <SelectItem v-for="state in states" :key="state.value" :value="state.value">
                                    {{ state.label }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <Button variant="outline" @click="handleClear">Clear</Button>
                <Button @click="handleApply">Apply Filters</Button>
            </div>
        </DialogContent>
    </Dialog>
</template>
