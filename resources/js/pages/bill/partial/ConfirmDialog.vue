<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';

interface Props {
    open: boolean;
}

interface Emits {
    (e: 'update:open', value: boolean): void;
    (e: 'confirm'): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const handleOpenChange = (value: boolean) => {
    emit('update:open', value);
};

const handleConfirm = () => {
    emit('confirm');
    emit('update:open', false);
};
</script>

<template>
    <Dialog :open="open" @update:open="handleOpenChange">
        <DialogContent class="z-[60]">
            <DialogHeader>
                <DialogTitle>Create Bill</DialogTitle>
                <DialogDescription> Are you sure you want to create this bill? This action cannot be undone. </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" @click="handleOpenChange(false)">Cancel</Button>
                <Button @click="handleConfirm">Confirm</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
