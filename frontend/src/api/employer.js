import { http } from './http'

export async function fetchEmployerStats() {
  const { data } = await http.get('/employer/stats')
  return data
}

export async function fetchMyJobs() {
  const { data } = await http.get('/employer/jobs')
  return data?.data ?? []
}

export async function toggleJobStatus(id, action) {
  const { data } = await http.patch(`/employer/jobs/${id}/${action}`)
  return data
}

export async function deleteJob(id) {
  await http.delete(`/employer/jobs/${id}`)
}
export async function createJob(jobData) {
  const { data } = await http.post('/employer/jobs', jobData)
  return data
}

export async function updateJob(id, jobData) {
  const { data } = await http.put(`/employer/jobs/${id}`, jobData)
  return data
}