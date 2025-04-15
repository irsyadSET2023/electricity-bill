<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import VPagination from '@/components/ui/pagination/VPagination.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { MoreVertical, Plus } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import StoreBillModal from './partial/StoreBillModal.vue';

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
        building_type: string;
        month: string;
        state: string;
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
const sortBy = ref(props.filters.sort_by || '');
const sortOrder = ref(props.filters.sort_order || '');
const selectedBuildingType = ref(props.filters.building_type || '');
const selectedMonth = ref(props.filters.month || '');
const selectedState = ref(props.filters.state || '');

// Add building types array
const buildingTypes = [
    { value: '', label: 'All Types' },
    { value: 'Resedential', label: 'Resedential' },
    { value: 'Commercial', label: 'Commercial' },
];

// Add months array from the existing monthIndex
const months = [
    { value: '', label: 'All Months' },
    ...Object.entries(monthIndex).map(([value, label]) => ({
        value: value.toString(),
        label,
    })),
];

// Add states array after buildingTypes array
const states = [
    { value: '', label: 'All States' },
    { value: 'Johor', label: 'Johor' },
    { value: 'Kedah', label: 'Kedah' },
    { value: 'Kelantan', label: 'Kelantan' },
    { value: 'Melaka', label: 'Melaka' },
    { value: 'Negeri Sembilan', label: 'Negeri Sembilan' },
    { value: 'Pahang', label: 'Pahang' },
    { value: 'Pulau Pinang', label: 'Penang' },
    { value: 'Perak', label: 'Perak' },
    { value: 'Perlis', label: 'Perlis' },
    { value: 'Sabah', label: 'Sabah' },
    { value: 'Sarawak', label: 'Sarawak' },
    { value: 'Selangor', label: 'Selangor' },
    { value: 'Terengganu', label: 'Terengganu' },
    { value: 'Kuala Lumpur', label: 'Kuala Lumpur' },
    { value: 'Labuan', label: 'Labuan' },
    { value: 'Putrajaya', label: 'Putrajaya' },
];

// Debounced search function
const debouncedSearch = debounce((value: string) => {
    router.get(
        '/bills',
        {
            search: value,
            sort_by: sortBy.value,
            sort_order: sortOrder.value,
            building_type: selectedBuildingType.value,
            month: selectedMonth.value,
            state: selectedState.value,
        },
        {
            preserveState: false,
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

// Add this ref for modal visibility
const showCreateModal = ref(false);

// Add handlers for filter changes
const handleBuildingTypeChange = (value: string) => {
    selectedBuildingType.value = value;
    router.get(
        '/bills',
        {
            search: searchQuery.value,
            sort_by: sortBy.value,
            sort_order: sortOrder.value,
            building_type: value,
            month: selectedMonth.value,
            state: selectedState.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

const handleMonthChange = (value: string) => {
    selectedMonth.value = value;
    router.get(
        '/bills',
        {
            search: searchQuery.value,
            sort_by: sortBy.value,
            sort_order: sortOrder.value,
            building_type: selectedBuildingType.value,
            month: value,
            state: selectedState.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

// Add handler for state changes
const handleStateChange = (value: string) => {
    selectedState.value = value;
    router.get(
        '/bills',
        {
            search: searchQuery.value,
            sort_by: sortBy.value,
            sort_order: sortOrder.value,
            building_type: selectedBuildingType.value,
            month: selectedMonth.value,
            state: value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

// Update the clearFilters function to include state
const clearFilters = () => {
    searchQuery.value = '';
    sortBy.value = '';
    sortOrder.value = '';
    selectedBuildingType.value = '';
    selectedMonth.value = '';
    selectedState.value = '';

    router.get(
        '/bills',
        {},
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};
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

                            <!-- <select
                                v-model="selectedBuildingType"
                                @change="handleBuildingTypeChange($event?.target?.value || '')"
                                class="rounded-md border px-3 py-2"
                            >
                                <option v-for="type in buildingTypes" :key="type.value" :value="type.value">
                                    {{ type.label }}
                                </option>
                            </select>

                            <select
                                v-model="selectedMonth"
                                @change="handleMonthChange($event?.target?.value || '')"
                                class="rounded-md border px-3 py-2"
                            >
                                <option v-for="month in months" :key="month.value" :value="month.value">
                                    {{ month.label }}
                                </option>
                            </select>

                            <select
                                v-model="selectedState"
                                @change="handleStateChange($event?.target?.value || '')"
                                class="rounded-md border px-3 py-2"
                            >
                                <option v-for="state in states" :key="state.value" :value="state.value">
                                    {{ state.label }}
                                </option>
                            </select> -->
                        </div>
                        <div class="flex items-center gap-4">
                            <Button variant="outline" @click="clearFilters"> Clear Filters </Button>
                            <Button @click="showCreateModal = true">
                                <Plus />
                                Add Bill
                            </Button>
                        </div>
                    </div>

                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Bill ID</TableHead>
                                    <TableHead @click="handleSort('month')">
                                        Month
                                        <span v-if="sortBy === 'month'" class="ml-2">
                                            {{ sortOrder === 'asc' ? '↑' : '↓' }}
                                        </span>
                                    </TableHead>
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

        <!-- Add the modal component to your template (near the end, before </AppLayout>) -->
        <StoreBillModal v-model:show="showCreateModal" />
    </AppLayout>
</template>
