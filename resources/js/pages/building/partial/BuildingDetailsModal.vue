<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Table, TableBody, TableCell, TableHead, TableRow } from '@/components/ui/table';
import { computed } from 'vue';

interface Building {
    id: string;
    name: string;
    building_type: string;
    state: string;
    created_at: string;
    owner: {
        id: string;
        name: string;
        email: string;
    };
    bills_summary: {
        total_bills: number;
        total_usage: number;
        total_amount: number;
        latest_bill_month: string | null;
    };
}

const props = defineProps<{
    show: boolean;
    building: Building | null;
}>();

const emit = defineEmits<{
    (e: 'update:show', value: boolean): void;
}>();

const formattedDate = computed(() => {
    if (!props.building?.created_at) return '';
    return new Date(props.building.created_at).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
});
</script>

<template>
    <Dialog :open="show" @update:open="(value) => emit('update:show', value)">
        <DialogContent class="max-h-[90vh] max-w-2xl overflow-y-auto">
            <DialogHeader class="bg-background sticky top-0 z-10 border-b pb-4">
                <DialogTitle>Building Details</DialogTitle>
            </DialogHeader>

            <div v-if="building" class="space-y-6">
                <!-- Basic Information -->
                <Card>
                    <CardHeader>
                        <CardTitle>Basic Information</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableBody>
                                <TableRow>
                                    <TableHead class="w-[200px]">Building ID</TableHead>
                                    <TableCell>{{ building.id }}</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableHead>Building Name</TableHead>
                                    <TableCell>{{ building.name }}</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableHead>Building Type</TableHead>
                                    <TableCell>{{ building.building_type }}</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableHead>State</TableHead>
                                    <TableCell>{{ building.state }}</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableHead>Registration Date</TableHead>
                                    <TableCell>{{ formattedDate }}</TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <!-- Owner Information -->
                <Card>
                    <CardHeader>
                        <CardTitle>Owner Information</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableBody>
                                <TableRow>
                                    <TableHead class="w-[200px]">Owner Name</TableHead>
                                    <TableCell>{{ building.owner.name }}</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableHead>Email</TableHead>
                                    <TableCell>{{ building.owner.email }}</TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <!-- Bills Summary -->
                <Card>
                    <CardHeader>
                        <CardTitle>Bills Summary</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableBody>
                                <TableRow>
                                    <TableHead class="w-[200px]">Total Bills</TableHead>
                                    <TableCell>{{ building.bills_summary.total_bills }}</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableHead>Total Usage</TableHead>
                                    <TableCell>{{ building.bills_summary.total_usage }} kWh</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableHead>Total Amount</TableHead>
                                    <TableCell>RM {{ building.bills_summary.total_amount.toFixed(2) }}</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableHead>Latest Bill Month</TableHead>
                                    <TableCell>{{ building.bills_summary.latest_bill_month || 'No bills yet' }}</TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>

            <div class="bg-background sticky bottom-0 z-10 mt-6 border-t pt-4">
                <div class="flex justify-end">
                    <Button variant="outline" @click="emit('update:show', false)">Close</Button>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
