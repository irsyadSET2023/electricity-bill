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
import { MoreVerticalIcon, Plus } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import BuildingDetailsModal from './partial/BuildingDetailsModal.vue';
import ConfirmDialog from './partial/ConfirmDialog.vue';
import StoreBuildingModal from './partial/StoreBuildingModal.vue';
import UpdateBuildingModal from './partial/UpdateBuildingModal.vue';

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
}

interface Props {
    buildings: {
        data: Building[];
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
        title: 'Buildings',
        href: '/buildings',
    },
];

const searchQuery = ref(props.filters.search || '');
const sortBy = ref(props.filters.sort_by || 'name');
const sortOrder = ref(props.filters.sort_order || 'asc');
const showCreateModal = ref(false);
const showDetailsModal = ref(false);
const showUpdateModal = ref(false);
const selectedBuilding = ref<Building | null>(null);
const showDeleteConfirmDialog = ref(false);
const buildingToDelete = ref<Building | null>(null);

// Debounced search function
const debouncedSearch = debounce((value: string) => {
    router.get(
        '/buildings',
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

// Add these functions for handling building actions
const viewBuildingDetails = (building: Building) => {
    selectedBuilding.value = building;
    showDetailsModal.value = true;
};

const editBuilding = (building: Building) => {
    selectedBuilding.value = building;
    showUpdateModal.value = true;
};

const deleteBuilding = (building: Building) => {
    buildingToDelete.value = building;
    showDeleteConfirmDialog.value = true;
};

const handleDeleteConfirm = () => {
    if (!buildingToDelete.value) return;

    router.delete(`/buildings/${buildingToDelete.value.id}`, {
        onSuccess: () => {
            buildingToDelete.value = null;
            showDeleteConfirmDialog.value = false;
        },
    });
};

// Handle sort changes
const handleSort = (column: string) => {
    const newSortOrder = sortBy.value === column && sortOrder.value === 'asc' ? 'desc' : 'asc';
    sortBy.value = column;
    sortOrder.value = newSortOrder;

    router.get(
        '/buildings',
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
        '/buildings',
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

// Add computed properties
const totalItems = computed(() => props.buildings.total);
const itemsPerPage = computed(() => props.buildings.per_page);
const totalPages = computed(() => props.buildings.last_page);
</script>

<template>
    <Head title="Buildings" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card>
                <CardHeader>
                    <CardTitle>Buildings</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <Input v-model="searchQuery" placeholder="Search buildings..." class="w-[300px]" />
                        </div>

                        <div class="flex items-center gap-4">
                            <Button @click="showCreateModal = true">
                                <Plus />
                                Add Building
                            </Button>
                        </div>
                    </div>

                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="cursor-pointer" @click="handleSort('name')">
                                        Name
                                        <span v-if="sortBy === 'name'" class="ml-2">
                                            {{ sortOrder === 'asc' ? '↑' : '↓' }}
                                        </span>
                                    </TableHead>
                                    <TableHead class="cursor-pointer" @click="handleSort('building_type')">
                                        Building Type
                                        <span v-if="sortBy === 'building_type'" class="ml-2">
                                            {{ sortOrder === 'asc' ? '↑' : '↓' }}
                                        </span>
                                    </TableHead>
                                    <TableHead class="cursor-pointer" @click="handleSort('state')">
                                        State
                                        <span v-if="sortBy === 'state'" class="ml-2">
                                            {{ sortOrder === 'asc' ? '↑' : '↓' }}
                                        </span>
                                    </TableHead>
                                    <TableHead>Owner</TableHead>
                                    <TableHead class="cursor-pointer" @click="handleSort('created_at')">
                                        Registered Date
                                        <span v-if="sortBy === 'created_at'" class="ml-2">
                                            {{ sortOrder === 'asc' ? '↑' : '↓' }}
                                        </span>
                                    </TableHead>
                                    <TableHead>Action</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="building in buildings.data" :key="building.id">
                                    <TableCell>{{ building.name }}</TableCell>
                                    <TableCell>{{ building.building_type }}</TableCell>
                                    <TableCell>{{ building.state }}</TableCell>
                                    <TableCell>{{ building.owner.name }}</TableCell>
                                    <TableCell>
                                        {{ new Date(building.created_at).toLocaleDateString() }}
                                    </TableCell>
                                    <TableCell>
                                        <DropdownMenu>
                                            <DropdownMenuTrigger
                                                class="hover:bg-muted flex h-8 w-8 items-center justify-center rounded-md border transition-colors"
                                            >
                                                <MoreVerticalIcon />
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent align="end">
                                                <DropdownMenuItem @click="() => viewBuildingDetails(building)"> View Details </DropdownMenuItem>
                                                <DropdownMenuItem @click="() => editBuilding(building)"> Edit Building </DropdownMenuItem>
                                                <DropdownMenuItem class="text-red-600" @click="() => deleteBuilding(building)">
                                                    Delete Building
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
                            :current-page="buildings.current_page"
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

    <StoreBuildingModal v-model:show="showCreateModal" />
    <BuildingDetailsModal v-model:show="showDetailsModal" :building="selectedBuilding" />
    <UpdateBuildingModal v-model:show="showUpdateModal" :building="selectedBuilding" />
    <ConfirmDialog
        v-model:open="showDeleteConfirmDialog"
        title="Delete Building"
        description="Are you sure you want to delete this building? This action cannot be undone."
        @confirm="handleDeleteConfirm"
    />
</template>
