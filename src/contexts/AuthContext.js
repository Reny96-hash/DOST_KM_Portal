import React, { createContext, useState, useContext, useEffect } from 'react';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { apiRequest } from '../services/api';

const AuthContext = createContext();

export const useAuth = () => useContext(AuthContext);

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [token, setToken] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadStoredAuth();
  }, []);

  const loadStoredAuth = async () => {
    try {
      const storedToken = await AsyncStorage.getItem('token');
      const storedUser = await AsyncStorage.getItem('user');
      
      if (storedToken && storedUser) {
        setToken(storedToken);
        setUser(JSON.parse(storedUser));
      }
    } catch (error) {
      console.error('Failed to load auth:', error);
    } finally {
      setLoading(false);
    }
  };

  const login = async (userData, authToken) => {
    setUser(userData);
    setToken(authToken);
    await AsyncStorage.setItem('token', authToken);
    await AsyncStorage.setItem('user', JSON.stringify(userData));
  };

  const logout = async () => {
    setUser(null);
    setToken(null);
    await AsyncStorage.removeItem('token');
    await AsyncStorage.removeItem('user');
  };

  const isAdmin = () => {
    return user?.role === 'admin' || user?.is_admin === true;
  };

  const isAuthor = () => {
    return user?.role === 'author';
  };

  const isAuthenticated = () => {
    return !!user;
  };

  return (
    <AuthContext.Provider value={{
      user,
      token,
      loading,
      login,
      logout,
      isAdmin,
      isAuthor,
      isAuthenticated
    }}>
      {children}
    </AuthContext.Provider>
  );
};