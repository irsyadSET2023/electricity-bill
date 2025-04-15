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
import { MoreVerticalIcon } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface User {
    id: string;
    name: string;
    email: string;
    created_at: string;
}

interface Props {
    users: {
        data: User[];
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
        title: 'Users',
        href: '/users',
    },
];

const searchQuery = ref(props.filters.search || '');
const sortBy = ref(props.filters.sort_by || 'name');
const sortOrder = ref(props.filters.sort_order || 'asc');

// Debounced search function
const debouncedSearch = debounce((value: string) => {
    router.get(
        '/users',
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

// Add these functions for handling user actions
const viewUserDetails = (user: User) => {
    // For now just console.log the user details
    console.log('View user details:', user);
    // Later you can implement navigation to user details page or show a modal
};

const editUser = (user: User) => {
    console.log('Edit user:', user);
    // Later you can implement navigation to edit page or show edit modal
};

const deleteUser = (user: User) => {
    console.log('Delete user:', user);
    // Later you can implement delete confirmation and API call
};

// Handle sort changes
const handleSort = (column: string) => {
    const newSortOrder = sortBy.value === column && sortOrder.value === 'asc' ? 'desc' : 'asc';
    sortBy.value = column;
    sortOrder.value = newSortOrder;

    router.get(
        '/users',
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
        '/users',
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

// Add a computed property for total items
const totalItems = computed(() => props.users.total);
const itemsPerPage = computed(() => props.users.per_page);

// Add computed property for total pages
const totalPages = computed(() => props.users.last_page);
</script>

<template>
    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <Card>
                <CardHeader>
                    <CardTitle>Users</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <Input v-model="searchQuery" placeholder="Search users..." class="w-[300px]" />
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
                                    <TableHead class="cursor-pointer" @click="handleSort('email')">
                                        Email
                                        <span v-if="sortBy === 'email'" class="ml-2">
                                            {{ sortOrder === 'asc' ? '↑' : '↓' }}
                                        </span>
                                    </TableHead>
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
                                <TableRow v-for="user in users.data" :key="user.id">
                                    <TableCell>{{ user.name }}</TableCell>
                                    <TableCell>{{ user.email }}</TableCell>

                                    <TableCell>
                                        {{ new Date(user.created_at).toLocaleDateString() }}
                                    </TableCell>
                                    <TableCell>
                                        <DropdownMenu>
                                            <DropdownMenuTrigger
                                                class="hover:bg-muted flex h-8 w-8 items-center justify-center rounded-md border transition-colors"
                                            >
                                                <MoreVerticalIcon />
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent align="end">
                                                <DropdownMenuItem @click="() => viewUserDetails(user)"> View Details </DropdownMenuItem>
                                                <DropdownMenuItem @click="() => editUser(user)"> Edit User </DropdownMenuItem>
                                                <DropdownMenuItem class="text-red-600" @click="() => deleteUser(user)">
                                                    Delete User
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
                            :current-page="users.current_page"
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
