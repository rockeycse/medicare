import { ref } from 'vue'

export function usePagination(initialPage = 1) {
  const currentPage = ref(initialPage)
  const lastPage = ref(1)
  const total = ref(0)
  const perPage = ref(15)

  function setMeta(meta: { current_page: number; last_page: number; total: number; per_page?: number }) {
    currentPage.value = meta.current_page
    lastPage.value = meta.last_page
    total.value = meta.total
    if (meta.per_page) perPage.value = meta.per_page
  }

  function goToPage(page: number) {
    if (page >= 1 && page <= lastPage.value) {
      currentPage.value = page
    }
  }

  return { currentPage, lastPage, total, perPage, setMeta, goToPage }
}