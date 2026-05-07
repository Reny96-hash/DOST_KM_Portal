import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
  StyleSheet,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
} from 'react-native';
import { useAuth } from '../../contexts/AuthContext';
import { apiRequest } from '../../services/api';

const LoginScreen = ({ navigation }) => {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const { login } = useAuth();

  const handleLogin = async () => {
    if (!username || !password) {
      Alert.alert('Error', 'Please enter username and password');
      return;
    }

    setLoading(true);
    try {
      const response = await apiRequest('/auth/login', {
        method: 'POST',
        body: JSON.stringify({ username, password }),
      });

      login(response.user, response.token);
      navigation.replace('MainTabs');
    } catch (error) {
      Alert.alert('Login Failed', error.message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
    >
      <ScrollView contentContainerStyle={styles.scrollContainer}>
        <View style={styles.header}>
          <Text style={styles.title}>📚 Knowledge Base</Text>
          <Text style={styles.subtitle}>Login to your account</Text>
        </View>

        <View style={styles.form}>
          <TextInput
            style={styles.input}
            placeholder="Username or Email"
            placeholderTextColor="#999"
            value={username}
            onChangeText={setUsername}
            autoCapitalize="none"
          />

          <TextInput
            style={styles.input}
            placeholder="Password"
            placeholderTextColor="#999"
            value={password}
            onChangeText={setPassword}
            secureTextEntry
          />

          <TouchableOpacity
            style={styles.loginButton}
            onPress={handleLogin}
            disabled={loading}
          >
            {loading ? (
              <ActivityIndicator color="#fff" />
            ) : (
              <Text style={styles.loginButtonText}>Login</Text>
            )}
          </TouchableOpacity>

          <TouchableOpacity
            onPress={() => navigation.navigate('Register')}
          >
            <Text style={styles.registerLink}>
              Don't have an account? Register
            </Text>
          </TouchableOpacity>
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f5f5f5' },
  scrollContainer: { flexGrow: 1, justifyContent: 'center', padding: 20 },
  header: { alignItems: 'center', marginBottom: 40 },
  title: { fontSize: 32, fontWeight: 'bold', color: '#1a1a1a', marginBottom: 8 },
  subtitle: { fontSize: 16, color: '#666' },
  form: { backgroundColor: '#fff', borderRadius: 12, padding: 20, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.1, shadowRadius: 4, elevation: 3 },
  input: { backgroundColor: '#f8f8f8', borderRadius: 8, padding: 14, fontSize: 16, marginBottom: 12, borderWidth: 1, borderColor: '#e0e0e0' },
  loginButton: { backgroundColor: '#007AFF', borderRadius: 8, padding: 14, alignItems: 'center', marginTop: 12 },
  loginButtonText: { color: '#fff', fontSize: 16, fontWeight: '600' },
  registerLink: { textAlign: 'center', marginTop: 20, color: '#007AFF', fontSize: 14 },
});

export default LoginScreen;