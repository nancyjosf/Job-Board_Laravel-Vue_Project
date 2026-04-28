import { http } from './http'

export async function fetchCategories() {
  const { data } = await http.get('/categories')
  return data?.data ?? []
}

export async function fetchJobs(params) {
  const { data } = await http.get('/jobs', { params })
  return data
}

export async function fetchJobById(id) {
  const { data } = await http.get(`/jobs/${id}`)
  return data?.data
}

