import AsyncStorage from '@react-native-async-storage/async-storage';

//const API_BASE_URL = 'https://platypus01.ifn666.com/api';
const API_BASE_URL = 'http:// 192.168.4.24:4000/api';


const getToken = async () => {
  return await AsyncStorage.getItem('token');
};

const handleResponse = async (response) => {
  if (!response.ok) {
    const error = await response.json().catch(() => ({}));
    throw new Error(error.error || error.message || `HTTP ${response.status}`);
  }
  return response.json();
};

export const apiRequest = async (endpoint, options = {}) => {
  const token = await getToken();
  
  const headers = {
    'Content-Type': 'application/json',
    ...options.headers,
  };
  
  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }
  
  const config = {
    ...options,
    headers,
  };
  
  try {
    const response = await fetch(`${API_BASE_URL}${endpoint}`, config);
    return await handleResponse(response);
  } catch (error) {
    console.error('API Error:', error);
    throw error;
  }
};

export const apiFormData = async (endpoint, formData, method = 'POST') => {
  const token = await getToken();
  
  const headers = {};
  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }
  
  const config = {
    method,
    headers,
    body: formData,
  };
  
  try {
    const response = await fetch(`${API_BASE_URL}${endpoint}`, config);
    return await handleResponse(response);
  } catch (error) {
    console.error('API Error:', error);
    throw error;
  }
};