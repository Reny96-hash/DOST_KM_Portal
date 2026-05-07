import React from 'react';
import { createStackNavigator } from '@react-navigation/stack';
import { useAuth } from '../contexts/AuthContext';
import AuthStack from './AuthStack';
import MainTabs from './MainTabs';
import { ActivityIndicator, View } from 'react-native';

const Stack = createStackNavigator();

const AppNavigator = () => {
  const { isAuthenticated, loading } = useAuth();

  if (loading) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <ActivityIndicator size="large" />
      </View>
    );
  }

  return (
    <Stack.Navigator screenOptions={{ headerShown: false }}>
      {isAuthenticated() ? (
        <Stack.Screen name="MainTabs" component={MainTabs} />
      ) : (
        <Stack.Screen name="AuthStack" component={AuthStack} />
      )}
    </Stack.Navigator>
  );
};

export default AppNavigator;