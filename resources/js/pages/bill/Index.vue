<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import VPagination from '@/components/ui/pagination/VPagination.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { MoreVertical } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface Bill {
    id: string;
    month: string;
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

interface Props {
    bills: {
        data: Bill[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
    filters: {
        search: string;
        sort_by: string;
        sort_order: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Bills',
        href: '/bills',
    },
];

const searchQuery = ref(props.filters.search || '');
const sortBy = ref(props.filters.sort_by || 'created_at');
const sortOrder = ref(props.filters.sort_order || 'desc');

// Debounced search function
const debouncedSearch = debounce((value: string) => {
    router.get(
        '/bills',
        {
            search: value,
            sort_by: sortBy.value,
            sort_order: sortOrder.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
}, 300);

// Watch for search input changes
watch(searchQuery, (value) => {
    debouncedSearch(value);
});

// Handle sort changes
const handleSort = (column: string) => {
    const newSortOrder = sortBy.value === column && sortOrder.value === 'asc' ? 'desc' : 'asc';
    sortBy.value = column;
    sortOrder.value = newSortOrder;

    router.get(
        '/bills',
        {
            search: searchQuery.value,
            sort_by: column,
            sort_order: newSortOrder,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

// Handle page change
const handlePageChange = (page: number) => {
    router.get(
        '/bills',
        {
            page,
            search: searchQuery.value,
            sort_by: sortBy.value,
            sort_order: sortOrder.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

// Format currency
const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'MYR',
    }).format(amount);
};

const viewBillDetails = (bill: Bill) => {
    console.log('View bill details:', bill);
};

const editBill = (bill: Bill) => {
    console.log('Edit bill:', bill);
};

const deleteBill = (bill: Bill) => {
    console.log('Delete bill:', bill);
};
// Computed properties
const totalPages = computed(() => props.bills.last_page);
</script>

<template>
    <Head title="Bills" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card>
                <CardHeader>
                    <CardTitle>Bills</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <Input v-model="searchQuery" placeholder="Search bills..." class="w-[300px]" />
                        </div>
                    </div>

                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Bill ID</TableHead>
                                    <TableHead>Month</TableHead>
                                    <TableHead>Building</TableHead>
                                    <TableHead>Owner</TableHead>

                                    <TableHead class="cursor-pointer" @click="handleSort('usability')">
                                        Usability (kWh)
                                        <span v-if="sortBy === 'usability'" class="ml-2">
                                            {{ sortOrder === 'asc' ? '↑' : '↓' }}
                                        </span>
                                    </TableHead>
                                    <TableHead>Total Amount</TableHead>
                                    <TableHead>Action</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="bill in bills.data" :key="bill.id">
                                    <TableCell>{{ bill.id }}</TableCell>
                                    <TableCell>{{ monthIndex[Number(bill.month)] }}</TableCell>
                                    <TableCell>
                                        {{ bill.building.name }}
                                        <span class="ml-2 text-xs text-gray-500">({{ bill.building.building_type }})</span>
                                    </TableCell>
                                    <TableCell>
                                        {{ bill.owner.name }}
                                        <div class="text-xs text-gray-500">{{ bill.owner.email }}</div>
                                    </TableCell>
                                    <TableCell>{{ bill.usability }}</TableCell>
                                    <TableCell>{{ formatCurrency(bill.total_amount) }}</TableCell>
                                    <TableCell>
                                        <DropdownMenu>
                                            <DropdownMenuTrigger
                                                class="hover:bg-muted flex h-8 w-8 items-center justify-center rounded-md border transition-colors"
                                            >
                                                <MoreVertical />
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent align="end">
                                                <DropdownMenuItem @click="() => viewBillDetails(bill)"> View Details </DropdownMenuItem>
                                                <DropdownMenuItem @click="() => editBill(bill)"> Edit Bill </DropdownMenuItem>
                                                <DropdownMenuItem class="text-red-600" @click="() => deleteBill(bill)">
                                                    Delete Bill
                                                </DropdownMenuItem>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        <VPagination
                            :current-page="bills.current_page"
                            :total-pages="totalPages"
                            :max-visible-pages="5"
                            show-edges
                            @update:page="handlePageChange"
                        />
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
