import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  TextInput,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
  StyleSheet,
} from 'react-native';
import { Picker } from '@react-native-picker/picker';
import { articleService } from '../../services/articleService';
import { topicService } from '../../services/topicService';

const SubmitArticleScreen = ({ route, navigation }) => {
  const editingArticle = route.params?.article;
  const [topics, setTopics] = useState([]);
  const [loading, setLoading] = useState(false);
  const [fetching, setFetching] = useState(true);
  const [formData, setFormData] = useState({
    title: editingArticle?.title || '',
    summary: editingArticle?.summary || '',
    content: editingArticle?.content || '',
    topic: editingArticle?.topic?._id || editingArticle?.topic || '',
  });

  useEffect(() => {
    loadTopics();
  }, []);

  const loadTopics = async () => {
    try {
      const data = await topicService.getTopics();
      setTopics(data);
    } catch (err) {
      Alert.alert('Error', 'Failed to load topics');
    } finally {
      setFetching(false);
    }
  };

  const handleSubmit = async () => {
    if (!formData.title || !formData.summary || !formData.content || !formData.topic) {
      Alert.alert('Error', 'Please fill all fields');
      return;
    }

    setLoading(true);
    try {
      if (editingArticle) {
        await articleService.updateArticle(editingArticle._id, formData);
        Alert.alert('Success', 'Article updated successfully');
      } else {
        await articleService.createArticle(formData);
        Alert.alert('Success', 'Article submitted for review');
      }
      navigation.goBack();
    } catch (err) {
      Alert.alert('Error', err.message);
    } finally {
      setLoading(false);
    }
  };

  if (fetching) {
    return (
      <View style={styles.centered}>
        <ActivityIndicator size="large" />
      </View>
    );
  }

  return (
    <ScrollView style={styles.container}>
      <Text style={styles.title}>{editingArticle ? 'Edit Article' : 'Submit New Article'}</Text>

      <Text style={styles.label}>Title *</Text>
      <TextInput
        style={styles.input}
        placeholder="Enter article title (5-200 characters)"
        value={formData.title}
        onChangeText={(text) => setFormData({ ...formData, title: text })}
      />

      <Text style={styles.label}>Summary *</Text>
      <TextInput
        style={[styles.input, styles.textArea]}
        placeholder="Brief summary (10-500 characters)"
        value={formData.summary}
        onChangeText={(text) => setFormData({ ...formData, summary: text })}
        multiline
        numberOfLines={3}
      />

      <Text style={styles.label}>Topic *</Text>
      <View style={styles.pickerContainer}>
        <Picker
          selectedValue={formData.topic}
          onValueChange={(value) => setFormData({ ...formData, topic: value })}
        >
          <Picker.Item label="Select a topic" value="" />
          {topics.map((topic) => (
            <Picker.Item key={topic._id} label={topic.name} value={topic._id} />
          ))}
        </Picker>
      </View>

      <Text style={styles.label}>Content *</Text>
      <TextInput
        style={[styles.input, styles.contentArea]}
        placeholder="Write your article content here (minimum 20 characters)"
        value={formData.content}
        onChangeText={(text) => setFormData({ ...formData, content: text })}
        multiline
        numberOfLines={15}
      />

      <TouchableOpacity
        style={styles.submitButton}
        onPress={handleSubmit}
        disabled={loading}
      >
        {loading ? (
          <ActivityIndicator color="#fff" />
        ) : (
          <Text style={styles.submitButtonText}>
            {editingArticle ? 'Update Article' : 'Submit for Review'}
          </Text>
        )}
      </TouchableOpacity>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f5f5f5', padding: 16 },
  centered: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  title: { fontSize: 28, fontWeight: 'bold', color: '#1a1a1a', marginBottom: 20, marginTop: 10 },
  label: { fontSize: 14, fontWeight: '600', color: '#333', marginBottom: 6, marginTop: 12 },
  input: { backgroundColor: '#fff', borderRadius: 8, padding: 12, fontSize: 16, borderWidth: 1, borderColor: '#e0e0e0' },
  textArea: { minHeight: 80, textAlignVertical: 'top' },
  contentArea: { minHeight: 250, textAlignVertical: 'top' },
  pickerContainer: { backgroundColor: '#fff', borderRadius: 8, borderWidth: 1, borderColor: '#e0e0e0', marginBottom: 12 },
  submitButton: { backgroundColor: '#007AFF', borderRadius: 8, padding: 16, alignItems: 'center', marginTop: 30, marginBottom: 40 },
  submitButtonText: { color: '#fff', fontSize: 16, fontWeight: '600' },
});

export default SubmitArticleScreen;