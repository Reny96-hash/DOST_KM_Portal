import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  ScrollView,
  ActivityIndicator,
  TouchableOpacity,
  Share,
  StyleSheet,
  Alert,
} from 'react-native';
import { articleService } from '../../services/articleService';
import { useAuth } from '../../contexts/AuthContext';

const ArticleDetailScreen = ({ route, navigation }) => {
  const { id } = route.params;
  const [article, setArticle] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const { isAuthenticated, isAdmin, user } = useAuth();

  useEffect(() => {
    loadArticle();
  }, [id]);

  const loadArticle = async () => {
    try {
      setLoading(true);
      const data = await articleService.getArticle(id);
      setArticle(data.data || data);
      setError(null);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'approved': return '#2ecc71';
      case 'pending': return '#f1c40f';
      case 'rejected': return '#e74c3c';
      default: return '#95a5a6';
    }
  };

  const handleShare = async () => {
    try {
      await Share.share({
        title: article.title,
        message: `${article.title}\n\n${article.summary}\n\nRead more on Knowledge Base`,
      });
    } catch (error) {
      Alert.alert('Error', 'Failed to share');
    }
  };

  const handleEdit = () => {
    navigation.navigate('SubmitArticle', { article });
  };

  const handleDelete = async () => {
    Alert.alert(
      'Delete Article',
      'Are you sure you want to delete this article?',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Delete',
          style: 'destructive',
          onPress: async () => {
            try {
              await articleService.deleteArticle(id);
              navigation.goBack();
            } catch (err) {
              Alert.alert('Error', err.message);
            }
          },
        },
      ],
    );
  };

  const canEdit = () => {
    if (!isAuthenticated()) return false;
    if (isAdmin()) return true;
    return article?.author?._id === user?._id;
  };

  if (loading) {
    return (
      <View style={styles.centered}>
        <ActivityIndicator size="large" />
      </View>
    );
  }

  if (error) {
    return (
      <View style={styles.centered}>
        <Text style={styles.errorText}>{error}</Text>
        <TouchableOpacity style={styles.retryButton} onPress={loadArticle}>
          <Text style={styles.retryText}>Retry</Text>
        </TouchableOpacity>
      </View>
    );
  }

  if (!article) {
    return (
      <View style={styles.centered}>
        <Text>Article not found</Text>
      </View>
    );
  }

  return (
    <ScrollView style={styles.container}>
      {/* Badges */}
      <View style={styles.badgeContainer}>
        <View style={[styles.statusBadge, { backgroundColor: getStatusColor(article.status) }]}>
          <Text style={styles.badgeText}>{article.status}</Text>
        </View>
        <View style={styles.topicBadge}>
          <Text style={styles.topicText}>{article.topic?.name || 'Uncategorized'}</Text>
        </View>
      </View>

      {/* Title */}
      <Text style={styles.title}>{article.title}</Text>

      {/* Metadata */}
      <Text style={styles.metadata}>
        By {article.author?.username || 'Unknown'} | {'\n'}
        📅 {new Date(article.createdAt).toLocaleDateString()} | 👁️ {article.views || 0} views
      </Text>

      {/* Summary */}
      <Text style={styles.summary}>{article.summary}</Text>

      {/* Content */}
      <Text style={styles.content}>{article.content}</Text>

      {/* Rejection Reason */}
      {article.status === 'rejected' && article.rejectionReason && (
        <View style={styles.rejectionContainer}>
          <Text style={styles.rejectionTitle}>Rejection Reason:</Text>
          <Text style={styles.rejectionText}>{article.rejectionReason}</Text>
        </View>
      )}

      {/* Action Buttons */}
      <View style={styles.actionContainer}>
        <TouchableOpacity style={styles.shareButton} onPress={handleShare}>
          <Text style={styles.shareButtonText}>📤 Share</Text>
        </TouchableOpacity>

        {canEdit() && (
          <>
            <TouchableOpacity style={styles.editButton} onPress={handleEdit}>
              <Text style={styles.editButtonText}>✏️ Edit</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.deleteButton} onPress={handleDelete}>
              <Text style={styles.deleteButtonText}>🗑️ Delete</Text>
            </TouchableOpacity>
          </>
        )}
      </View>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#fff', padding: 16 },
  centered: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  badgeContainer: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 16 },
  statusBadge: { paddingHorizontal: 12, paddingVertical: 4, borderRadius: 12 },
  badgeText: { color: '#fff', fontSize: 12, fontWeight: '600', textTransform: 'capitalize' },
  topicBadge: { backgroundColor: '#e3f2fd', paddingHorizontal: 12, paddingVertical: 4, borderRadius: 12 },
  topicText: { color: '#1976d2', fontSize: 12, fontWeight: '500' },
  title: { fontSize: 24, fontWeight: 'bold', color: '#1a1a1a', marginBottom: 12 },
  metadata: { fontSize: 12, color: '#666', marginBottom: 16, lineHeight: 18 },
  summary: { fontSize: 16, color: '#555', fontStyle: 'italic', marginBottom: 20, paddingBottom: 16, borderBottomWidth: 1, borderBottomColor: '#e0e0e0' },
  content: { fontSize: 15, color: '#333', lineHeight: 24, marginBottom: 20 },
  rejectionContainer: { backgroundColor: '#fee', padding: 12, borderRadius: 8, marginTop: 16, marginBottom: 16 },
  rejectionTitle: { fontWeight: 'bold', color: '#c0392b', marginBottom: 4 },
  rejectionText: { color: '#c0392b' },
  actionContainer: { flexDirection: 'row', justifyContent: 'space-around', marginTop: 20, marginBottom: 30, gap: 10 },
  shareButton: { flex: 1, backgroundColor: '#34B7F1', padding: 12, borderRadius: 8, alignItems: 'center' },
  shareButtonText: { color: '#fff', fontWeight: '600' },
  editButton: { flex: 1, backgroundColor: '#f39c12', padding: 12, borderRadius: 8, alignItems: 'center' },
  editButtonText: { color: '#fff', fontWeight: '600' },
  deleteButton: { flex: 1, backgroundColor: '#e74c3c', padding: 12, borderRadius: 8, alignItems: 'center' },
  deleteButtonText: { color: '#fff', fontWeight: '600' },
  errorText: { color: 'red', textAlign: 'center', margin: 20 },
  retryButton: { backgroundColor: '#007AFF', padding: 10, borderRadius: 8, marginTop: 10 },
  retryText: { color: '#fff', fontWeight: '600' },
});

export default ArticleDetailScreen;