import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  FlatList,
  TouchableOpacity,
  ActivityIndicator,
  RefreshControl,
  Alert,
  Modal,
  StyleSheet,
} from 'react-native';
import { userService } from '../../services/userService';
import { useAuth } from '../../contexts/AuthContext';

const ManageUsersScreen = () => {
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [selectedUser, setSelectedUser] = useState(null);
  const [roleModalVisible, setRoleModalVisible] = useState(false);
  const { token } = useAuth();

  useEffect(() => {
    loadUsers();
  }, []);

  const loadUsers = async () => {
    try {
      setLoading(true);
      const data = await userService.getAllUsers();
      setUsers(data || []);
    } catch (err) {
      Alert.alert('Error', err.message);
    } finally {
      setLoading(false);
    }
  };

  const onRefresh = async () => {
    setRefreshing(true);
    await loadUsers();
    setRefreshing(false);
  };

  const handleUpdateRole = async (role) => {
    try {
      await userService.updateUserRole(selectedUser._id, role);
      Alert.alert('Success', `Role updated to ${role}`);
      setRoleModalVisible(false);
      loadUsers();
    } catch (err) {
      Alert.alert('Error', err.message);
    }
  };

  const handleDeleteUser = (user) => {
    Alert.alert(
      'Delete User',
      `Are you sure you want to delete "${user.username}"? This will delete all their data.`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Delete',
          style: 'destructive',
          onPress: async () => {
            try {
              await userService.deleteUser(user._id);
              loadUsers();
            } catch (err) {
              Alert.alert('Error', err.message);
            }
          },
        },
      ],
    );
  };

  const getRoleColor = (role) => {
    switch (role) {
      case 'admin': return '#e74c3c';
      case 'author': return '#f39c12';
      default: return '#2ecc71';
    }
  };

  const renderUserItem = ({ item }) => (
    <View style={styles.userCard}>
      <View style={styles.userHeader}>
        <Text style={styles.username}>{item.username}</Text>
        <View style={[styles.roleBadge, { backgroundColor: getRoleColor(item.role) }]}>
          <Text style={styles.roleText}>{item.role || 'user'}</Text>
        </View>
      </View>

      <Text style={styles.userEmail}>{item.email}</Text>

      <View style={styles.userStats}>
        <Text style={styles.userStat}>Articles: {item.articleCount || 0}</Text>
        <Text style={styles.userStat}>Storage: {((item.storageUsed || 0) / 1024 / 1024).toFixed(1)} MB</Text>
      </View>

      <View style={styles.actionButtons}>
        <TouchableOpacity
          style={styles.editRoleButton}
          onPress={() => {
            setSelectedUser(item);
            setRoleModalVisible(true);
          }}
        >
          <Text style={styles.editRoleButtonText}>Edit Role</Text>
        </TouchableOpacity>
        <TouchableOpacity style={styles.deleteButton} onPress={() => handleDeleteUser(item)}>
          <Text style={styles.deleteButtonText}>Delete</Text>
        </TouchableOpacity>
      </View>
    </View>
  );

  if (loading && !refreshing) {
    return (
      <View style={styles.centered}>
        <ActivityIndicator size="large" />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <Text style={styles.header}>Manage Users ({users.length})</Text>

      <FlatList
        data={users}
        keyExtractor={(item) => item._id}
        renderItem={renderUserItem}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
        ListEmptyComponent={<Text style={styles.emptyText}>No users found</Text>}
      />

      <Modal visible={roleModalVisible} animationType="slide" transparent={true}>
        <View style={styles.modalContainer}>
          <View style={styles.modalContent}>
            <Text style={styles.modalTitle}>Update User Role</Text>
            <Text style={styles.modalSubtitle}>User: {selectedUser?.username}</Text>

            <TouchableOpacity
              style={[styles.roleOption, selectedUser?.role === 'user' && styles.roleOptionActive]}
              onPress={() => handleUpdateRole('user')}
            >
              <Text style={styles.roleOptionText}>User</Text>
              <Text style={styles.roleOptionDesc}>Basic user - can create content</Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.roleOption, selectedUser?.role === 'author' && styles.roleOptionActive]}
              onPress={() => handleUpdateRole('author')}
            >
              <Text style={styles.roleOptionText}>Author</Text>
              <Text style={styles.roleOptionDesc}>Can create and manage own content</Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.roleOption, selectedUser?.role === 'admin' && styles.roleOptionActive]}
              onPress={() => handleUpdateRole('admin')}
            >
              <Text style={styles.roleOptionText}>Admin</Text>
              <Text style={styles.roleOptionDesc}>Full access - can approve content</Text>
            </TouchableOpacity>

            <TouchableOpacity style={styles.modalCancel} onPress={() => setRoleModalVisible(false)}>
              <Text style={styles.modalCancelText}>Cancel</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
    </View>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f5f5f5', padding: 12 },
  centered: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header: { fontSize: 20, fontWeight: 'bold', marginBottom: 16, color: '#333' },
  userCard: { backgroundColor: '#fff', borderRadius: 12, padding: 16, marginBottom: 12, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.1, shadowRadius: 4, elevation: 2 },
  userHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 8 },
  username: { fontSize: 18, fontWeight: '600', color: '#333' },
  roleBadge: { paddingHorizontal: 12, paddingVertical: 4, borderRadius: 12 },
  roleText: { color: '#fff', fontSize: 12, fontWeight: '600', textTransform: 'capitalize' },
  userEmail: { fontSize: 14, color: '#666', marginBottom: 12 },
  userStats: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 12 },
  userStat: { fontSize: 12, color: '#999' },
  actionButtons: { flexDirection: 'row', justifyContent: 'flex-end', gap: 8 },
  editRoleButton: { backgroundColor: '#007AFF', paddingHorizontal: 16, paddingVertical: 8, borderRadius: 6 },
  editRoleButtonText: { color: '#fff', fontWeight: '600' },
  deleteButton: { backgroundColor: '#e74c3c', paddingHorizontal: 16, paddingVertical: 8, borderRadius: 6 },
  deleteButtonText: { color: '#fff', fontWeight: '600' },
  emptyText: { textAlign: 'center', marginTop: 50, color: '#666' },
  modalContainer: { flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: 'rgba(0,0,0,0.5)' },
  modalContent: { backgroundColor: '#fff', borderRadius: 12, padding: 20, width: '85%', maxWidth: 400 },
  modalTitle: { fontSize: 20, fontWeight: 'bold', marginBottom: 8, textAlign: 'center' },
  modalSubtitle: { fontSize: 14, color: '#666', textAlign: 'center', marginBottom: 20 },
  roleOption: { backgroundColor: '#f8f8f8', borderRadius: 8, padding: 12, marginBottom: 10, borderWidth: 1, borderColor: '#e0e0e0' },
  roleOptionActive: { backgroundColor: '#e3f2fd', borderColor: '#007AFF' },
  roleOptionText: { fontSize: 16, fontWeight: '600', color: '#333', marginBottom: 2 },
  roleOptionDesc: { fontSize: 11, color: '#666' },
  modalCancel: { backgroundColor: '#e0e0e0', borderRadius: 8, padding: 12, alignItems: 'center', marginTop: 10 },
  modalCancelText: { color: '#666', fontSize: 16, fontWeight: '500' },
});

export default ManageUsersScreen;