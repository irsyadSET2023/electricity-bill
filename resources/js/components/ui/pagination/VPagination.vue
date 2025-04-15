<script setup lang="ts">
import { computed } from 'vue';
import { Button } from '@/components/ui/button';

interface Props {
  currentPage: number;
  totalPages: number;
  maxVisiblePages?: number;
  showEdges?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  maxVisiblePages: 5,
  showEdges: true
});

const emit = defineEmits<{
  (e: 'update:page', page: number): void;
}>();

const pages = computed(() => {
  const pages: (number | string)[] = [];
  const halfVisible = Math.floor(props.maxVisiblePages / 2);
  let start = Math.max(1, props.currentPage - halfVisible);
  let end = Math.min(props.totalPages, start + props.maxVisiblePages - 1);

  if (end - start + 1 < props.maxVisiblePages) {
    start = Math.max(1, end - props.maxVisiblePages + 1);
  }

  // Add first page and ellipsis if needed
  if (props.showEdges && start > 1) {
    pages.push(1);
    if (start > 2) pages.push('...');
  }

  // Add visible page numbers
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }

  // Add last page and ellipsis if needed
  if (props.showEdges && end < props.totalPages) {
    if (end < props.totalPages - 1) pages.push('...');
    pages.push(props.totalPages);
  }

  return pages;
});

const isCurrentPage = (page: number | string) => page === props.currentPage;
const isDisabled = (direction: 'prev' | 'next') => {
  return direction === 'prev' ? props.currentPage === 1 : props.currentPage === props.totalPages;
};

const handlePageChange = (page: number) => {
  if (page >= 1 && page <= props.totalPages) {
    emit('update:page', page);
  }
};
</script>

<template>
  <nav class="flex items-center justify-center space-x-2" role="navigation" aria-label="Pagination">
    <!-- Previous button -->
    <Button
      variant="outline"
      class="h-10 w-10 p-0"
      :disabled="isDisabled('prev')"
      @click="handlePageChange(currentPage - 1)"
    >
      <span class="sr-only">Previous page</span>
      <svg
        class="h-4 w-4"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
    </Button>

    <!-- Page numbers -->
    <template v-for="(page, index) in pages" :key="index">
      <template v-if="typeof page === 'number'">
        <Button
          :variant="isCurrentPage(page) ? 'default' : 'outline'"
          class="h-10 w-10 p-0"
          @click="handlePageChange(page)"
        >
          {{ page }}
        </Button>
      </template>
      <template v-else>
        <span class="px-2">{{ page }}</span>
      </template>
    </template>

    <!-- Next button -->
    <Button
      variant="outline"
      class="h-10 w-10 p-0"
      :disabled="isDisabled('next')"
      @click="handlePageChange(currentPage + 1)"
    >
      <span class="sr-only">Next page</span>
      <svg
        class="h-4 w-4"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
    </Button>
  </nav>
</template> 