<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Table, TableBody, TableCell, TableHead, TableRow } from '@/components/ui/table';
import { computed } from 'vue';

interface Bill {
    id: string;
    month: number;
    usability: number;
    building: {
        id: string;
        name: string;
        building_type: string;
    };
    owner: {
        id: string;
        name: string;
        email: string;
    };
    created_at: string;
    total_amount: number;
}

const props = defineProps<{
    show: boolean;
    bill: Bill | null;
}>();

const emit = defineEmits<{
    (e: 'update:show', value: boolean): void;
}>();

const monthIndex: Record<number, string> = {
    1: 'January',
    2: 'February',
    3: 'March',
    4: 'April',
    5: 'May',
    6: 'June',
    7: 'July',
    8: 'August',
    9: 'September',
    10: 'October',
    11: 'November',
    12: 'December',
};

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'MYR',
    }).format(amount);
};

const formattedDate = computed(() => {
    if (!props.bill?.created_at) return '';
    return new Date(props.bill.created_at).toLocaleDateString('en-US', {
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
                <DialogTitle>Bill Details</DialogTitle>
            </DialogHeader>

            <div v-if="bill" class="space-y-6">
                <!-- Basic Information -->
                <Card>
                    <CardHeader>
                        <CardTitle>Basic Information</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableBody>
                                <TableRow>
                                    <TableHead class="w-[200px]">Bill ID</TableHead>
                                    <TableCell>{{ bill.id }}</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableHead>Month</TableHead>
                                    <TableCell>{{ monthIndex[Number(bill.month)] }}</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableHead>Created At</TableHead>
                                    <TableCell>{{ formattedDate }}</TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <!-- Building Information -->
                <Card>
                    <CardHeader>
                        <CardTitle>Building Information</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableBody>
                                <TableRow>
                                    <TableHead class="w-[200px]">Building Name</TableHead>
                                    <TableCell>{{ bill.building.name }}</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableHead>Building Type</TableHead>
                                    <TableCell>{{ bill.building.building_type }}</TableCell>
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
                                    <TableCell>{{ bill.owner.name }}</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableHead>Email</TableHead>
                                    <TableCell>{{ bill.owner.email }}</TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <!-- Usage and Amount -->
                <Card>
                    <CardHeader>
                        <CardTitle>Usage and Amount</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableBody>
                                <TableRow>
                                    <TableHead class="w-[200px]">Electricity Usage</TableHead>
                                    <TableCell>{{ bill.usability }} kWh</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableHead>Total Amount</TableHead>
                                    <TableCell class="font-semibold">{{ formatCurrency(bill.total_amount) }}</TableCell>
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
