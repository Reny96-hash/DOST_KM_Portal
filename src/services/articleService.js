import { apiRequest } from './api';

export const articleService = {
  // Public - get approved articles
  getArticles: (page = 1, sort = 'createdAt', order = 'desc', search = '') => {
    const url = `/articles/public?page=${page}&sort=${sort}&order=${order}&title=${search}&limit=10`;
    return apiRequest(url);
  },
  
  // Get single article
  getArticle: (id) => apiRequest(`/articles/${id}`),
  
  // Auth required - user's articles
  getMyArticles: () => apiRequest('/articles/my'),
  
  // Create article
  createArticle: (data) => apiRequest('/articles', {
    method: 'POST',
    body: JSON.stringify(data),
  }),
  
  // Update article
  updateArticle: (id, data) => apiRequest(`/articles/${id}`, {
    method: 'PUT',
    body: JSON.stringify(data),
  }),
  
  // Delete article
  deleteArticle: (id) => apiRequest(`/articles/${id}`, {
    method: 'DELETE',
  }),
  
  // Admin - get pending articles
  getPendingArticles: () => apiRequest('/articles/pending'),
  
  // Admin - approve article
  approveArticle: (id) => apiRequest(`/articles/${id}/approve`, {
    method: 'POST',
  }),
  
  // Admin - reject article
  rejectArticle: (id, reason) => apiRequest(`/articles/${id}/reject`, {
    method: 'POST',
    body: JSON.stringify({ reason }),
  }),
};