import { apiRequest } from './api';

export const topicService = {
  getTopics: () => apiRequest('/topics'),
  getTopic: (id) => apiRequest(`/topics/${id}`),
  createTopic: (data) => apiRequest('/topics', { method: 'POST', body: JSON.stringify(data) }),
  updateTopic: (id, data) => apiRequest(`/topics/${id}`, { method: 'PUT', body: JSON.stringify(data) }),
  deleteTopic: (id) => apiRequest(`/topics/${id}`, { method: 'DELETE' }),
};