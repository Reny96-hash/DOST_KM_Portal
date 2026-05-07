import { apiRequest } from './api';

export const userService = {
  getProfile: () => apiRequest('/users/profile'),
  updateProfile: (data) => apiRequest('/users/profile', { method: 'PUT', body: JSON.stringify(data) }),
  getAllUsers: () => apiRequest('/users'),
  updateUserRole: (id, role) => apiRequest(`/users/${id}`, { method: 'PUT', body: JSON.stringify({ role }) }),
  deleteUser: (id) => apiRequest(`/users/${id}`, { method: 'DELETE' }),
};