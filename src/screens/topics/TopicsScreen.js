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
  TextInput,
  StyleSheet,
} from 'react-native';
import { topicService } from '../../services/topicService';
import { useAuth } from '../../contexts/AuthContext';

const TopicsScreen = () => {
  const [topics, setTopics] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [modalVisible, setModalVisible] = useState(false);
  const [editingTopic, setEditingTopic] = useState(null);
  const [formData, setFormData] = useState({ name: '', description: '' });
  const { isAdmin, token } = useAuth();

  useEffect(() => {
    loadTopics();
  }, []);

  const loadTopics = async () => {
    try {
      setLoading(true);
      const data = await topicService.getTopics();
      setTopics(data || []);
    } catch (err) {
      Alert.alert('Error', err.message);
    } finally {
      setLoading(false);
    }
  };

  const onRefresh = async () => {
    setRefreshing(true);
    await loadTopics();
    setRefreshing(false);
  };

  const handleCreate = async () => {
    if (!formData.name) {
      Alert.alert('Error', 'Topic name is required');
      return;
    }

    try {
      await topicService.createTopic(formData);
      Alert.alert('Success', 'Topic created');
      setModalVisible(false);
      setFormData({ name: '', description: '' });
      loadTopics();
    } catch (err) {
      Alert.alert('Error', err.message);
    }
  };

  const handleUpdate = async () => {
    if (!formData.name) {
      Alert.alert('Error', 'Topic name is required');
      return;
    }

    try {
      await topicService.updateTopic(editingTopic._id, formData);
      Alert.alert('Success', 'Topic updated');
      setModalVisible(false);
      setEditingTopic(null);
      setFormData({ name: '', description: '' });
      loadTopics();
    } catch (err) {
      Alert.alert('Error', err.message);
    }
  };

  const handleDelete = (topic) => {
    Alert.alert(
      'Delete Topic',
      `Are you sure you want to delete "${topic.name}"? This will affect articles using this topic.`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Delete',
          style: 'destructive',
          onPress: async () => {
            try {
              await topicService.deleteTopic(topic._id);
              loadTopics();
            } catch (err) {
              Alert.alert('Error', err.message);
            }
          },
        },
      ],
    );
  };

  const openCreateModal = () => {
    setEditingTopic(null);
    setFormData({ name: '', description: '' });
    setModalVisible(true);
  };

  const openEditModal = (topic) => {
    setEditingTopic(topic);
    setFormData({ name: topic.name, description: topic.description || '' });
    setModalVisible(true);
  };

  const renderTopicItem = ({ item }) => (
    <View style={styles.topicCard}>
      <Text style={styles.topicName}>{item.name}</Text>
      <Text style={styles.topicDesc}>{item.description || 'No description'}</Text>
      {isAdmin() && (
        <View style={styles.adminActions}>
          <TouchableOpacity style={styles.editButton} onPress={() => openEditModal(item)}>
            <Text style={styles.editButtonText}>✏️ Edit</Text>
          </TouchableOpacity>
          <TouchableOpacity style={styles.deleteButton} onPress={() => handleDelete(item)}>
            <Text style={styles.deleteButtonText}>🗑️ Delete</Text>
          </TouchableOpacity>
        </View>
      )}
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
      {isAdmin() && (
        <TouchableOpacity style={styles.createButton} onPress={openCreateModal}>
          <Text style={styles.createButtonText}>+ Create New Topic</Text>
        </TouchableOpacity>
      )}

      <FlatList
        data={topics}
        keyExtractor={(item) => item._id}
        renderItem={renderTopicItem}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
        ListEmptyComponent={<Text style={styles.emptyText}>No topics available</Text>}
      />

      <Modal visible={modalVisible} animationType="slide" transparent={true}>
        <View style={styles.modalContainer}>
          <View style={styles.modalContent}>
            <Text style={styles.modalTitle}>
              {editingTopic ? 'Edit Topic' : 'Create Topic'}
            </Text>

            <TextInput
              style={styles.modalInput}
              placeholder="Topic Name *"
              value={formData.name}
              onChangeText={(text) => setFormData({ ...formData, name: text })}
            />

            <TextInput
              style={[styles.modalInput, styles.textArea]}
              placeholder="Description (optional)"
              value={formData.description}
              onChangeText={(text) => setFormData({ ...formData, description: text })}
              multiline
              numberOfLines={3}
            />

            <View style={styles.modalButtons}>
              <TouchableOpacity style={styles.modalCancel} onPress={() => setModalVisible(false)}>
                <Text style={styles.modalCancelText}>Cancel</Text>
              </TouchableOpacity>
              <TouchableOpacity style={styles.modalSubmit} onPress={editingTopic ? handleUpdate : handleCreate}>
                <Text style={styles.modalSubmitText}>{editingTopic ? 'Update' : 'Create'}</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
    </View>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f5f5f5', padding: 12 },
  centered: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  createButton: { backgroundColor: '#007AFF', borderRadius: 8, padding: 14, alignItems: 'center', marginBottom: 16 },
  createButtonText: { color: '#fff', fontSize: 16, fontWeight: '600' },
  topicCard: { backgroundColor: '#fff', borderRadius: 12, padding: 16, marginBottom: 12, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.1, shadowRadius: 4, elevation: 2 },
  topicName: { fontSize: 18, fontWeight: '600', color: '#333', marginBottom: 8 },
  topicDesc: { fontSize: 14, color: '#666', marginBottom: 12 },
  adminActions: { flexDirection: 'row', justifyContent: 'flex-end', gap: 8 },
  editButton: { paddingHorizontal: 12, paddingVertical: 6, borderRadius: 6, backgroundColor: '#f39c12' },
  editButtonText: { color: '#fff', fontSize: 12, fontWeight: '500' },
  deleteButton: { paddingHorizontal: 12, paddingVertical: 6, borderRadius: 6, backgroundColor: '#e74c3c' },
  deleteButtonText: { color: '#fff', fontSize: 12, fontWeight: '500' },
  emptyText: { textAlign: 'center', marginTop: 50, color: '#666' },
  modalContainer: { flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: 'rgba(0,0,0,0.5)' },
  modalContent: { backgroundColor: '#fff', borderRadius: 12, padding: 20, width: '85%', maxWidth: 400 },
  modalTitle: { fontSize: 20, fontWeight: 'bold', marginBottom: 16, textAlign: 'center' },
  modalInput: { backgroundColor: '#f8f8f8', borderRadius: 8, padding: 12, fontSize: 16, borderWidth: 1, borderColor: '#e0e0e0', marginBottom: 12 },
  textArea: { minHeight: 80, textAlignVertical: 'top' },
  modalButtons: { flexDirection: 'row', justifyContent: 'space-between', marginTop: 16, gap: 12 },
  modalCancel: { flex: 1, backgroundColor: '#e0e0e0', borderRadius: 8, padding: 12, alignItems: 'center' },
  modalCancelText: { color: '#666', fontSize: 16, fontWeight: '500' },
  modalSubmit: { flex: 1, backgroundColor: '#007AFF', borderRadius: 8, padding: 12, alignItems: 'center' },
  modalSubmitText: { color: '#fff', fontSize: 16, fontWeight: '600' },
});

export default TopicsScreen;