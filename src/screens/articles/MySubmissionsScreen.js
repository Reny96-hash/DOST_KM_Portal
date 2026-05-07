import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  FlatList,
  TouchableOpacity,
  ActivityIndicator,
  RefreshControl,
  StyleSheet,
  Alert,
} from 'react-native';
import { articleService } from '../../services/articleService';
import ArticleCard from '../../components/ArticleCard';

const MySubmissionsScreen = ({ navigation }) => {
  const [articles, setArticles] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [error, setError] = useState(null);
  const [activeTab, setActiveTab] = useState('all');

  useEffect(() => {
    loadArticles();
  }, []);

  const loadArticles = async () => {
    try {
      setLoading(true);
      const data = await articleService.getMyArticles();
      setArticles(data.data || data || []);
      setError(null);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  const onRefresh = async () => {
    setRefreshing(true);
    await loadArticles();
    setRefreshing(false);
  };

  const handleEdit = (article) => {
    navigation.navigate('SubmitArticle', { article });
  };

  const handleDelete = (article) => {
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
              await articleService.deleteArticle(article._id);
              loadArticles();
            } catch (err) {
              Alert.alert('Error', err.message);
            }
          },
        },
      ],
    );
  };

  const getFilteredArticles = () => {
    if (activeTab === 'all') return articles;
    return articles.filter(a => a.status === activeTab);
  };

  const getCount = (status) => {
    if (status === 'all') return articles.length;
    return articles.filter(a => a.status === status).length;
  };

  if (loading && !refreshing) {
    return (
      <View style={styles.centered}>
        <ActivityIndicator size="large" />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      {/* Tabs */}
      <View style={styles.tabContainer}>
        {['all', 'pending', 'approved', 'rejected'].map((tab) => (
          <TouchableOpacity
            key={tab}
            style={[styles.tab, activeTab === tab && styles.activeTab]}
            onPress={() => setActiveTab(tab)}
          >
            <Text style={[styles.tabText, activeTab === tab && styles.activeTabText]}>
              {tab.charAt(0).toUpperCase() + tab.slice(1)} ({getCount(tab)})
            </Text>
          </TouchableOpacity>
        ))}
      </View>

      {/* Articles List */}
      <FlatList
        data={getFilteredArticles()}
        keyExtractor={(item) => item._id}
        renderItem={({ item }) => (
          <ArticleCard
            article={item}
            showEdit={true}
            onEdit={handleEdit}
            onPress={(id) => navigation.navigate('ArticleDetail', { id })}
          />
        )}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
        }
        ListEmptyComponent={
          <Text style={styles.emptyText}>No articles found</Text>
        }
      />

      {/* New Article Button */}
      <TouchableOpacity
        style={styles.fab}
        onPress={() => navigation.navigate('SubmitArticle')}
      >
        <Text style={styles.fabText}>+</Text>
      </TouchableOpacity>
    </View>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f5f5f5' },
  centered: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  tabContainer: { flexDirection: 'row', backgroundColor: '#fff', paddingVertical: 8, borderBottomWidth: 1, borderBottomColor: '#e0e0e0' },
  tab: { flex: 1, paddingVertical: 10, alignItems: 'center', borderRadius: 8, marginHorizontal: 4 },
  activeTab: { backgroundColor: '#007AFF' },
  tabText: { fontSize: 12, color: '#666', fontWeight: '500' },
  activeTabText: { color: '#fff' },
  emptyText: { textAlign: 'center', marginTop: 50, color: '#666' },
  fab: { position: 'absolute', bottom: 20, right: 20, backgroundColor: '#007AFF', width: 56, height: 56, borderRadius: 28, alignItems: 'center', justifyContent: 'center', elevation: 5, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.25, shadowRadius: 4 },
  fabText: { fontSize: 28, color: '#fff', fontWeight: '600' },
});

export default MySubmissionsScreen;