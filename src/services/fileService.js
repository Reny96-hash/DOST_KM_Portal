import { apiRequest, apiFormData } from './api';

export const fileService = {
  // Get approved files
  getFiles: (page = 1, fileType = '', search = '') => {
    let url = `/resources?page=${page}&limit=12`;
    if (fileType) url += `&fileType=${fileType}`;
    if (search) url += `&search=${search}`;
    return apiRequest(url);
  },
  
  // Get user's own files
  getMyFiles: () => apiRequest('/resources/my'),
  
  // Admin - get pending files
  getPendingFiles: () => apiRequest('/resources/admin/pending'),
  
  // Upload file
  uploadFile: async (file, title, description) => {
    const formData = new FormData();
    formData.append('file', {
      uri: file.uri,
      name: file.name,
      type: file.mimeType || 'application/octet-stream',
    });
    formData.append('title', title);
    formData.append('description', description);
    
    return apiFormData('/resources/upload', formData);
  },
  
  // Download file
  getDownloadUrl: (id) => `${API_BASE_URL}/resources/${id}/download`,
  
  // Delete file
  deleteFile: (id) => apiRequest(`/resources/${id}`, {
    method: 'DELETE',
  }),
  
  // Approve file (admin)
  approveFile: (id) => apiRequest(`/resources/${id}/approve`, {
    method: 'POST',
  }),
  
  // Reject file (admin)
  rejectFile: (id, reason) => apiRequest(`/resources/${id}/reject`, {
    method: 'POST',
    body: JSON.stringify({ reason }),
  }),
};