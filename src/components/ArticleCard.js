import React from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  Pressable
} from 'react-native';

const ArticleCard = ({ article, onPress, showEdit = false, onEdit }) => {
  const getStatusColor = (status) => {
    switch (status) {
      case 'approved': return '#2ecc71';
      case 'pending': return '#f1c40f';
      case 'rejected': return '#e74c3c';
      default: return '#95a5a6';
    }
  };

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString();
  };

  return (
    <TouchableOpacity
      style={styles.card}
      onPress={() => onPress && onPress(article._id)}
      activeOpacity={0.7}
    >
      {/* Status and Topic Badges */}
      <View style={styles.badgeContainer}>
        <View style={[styles.statusBadge, { backgroundColor: getStatusColor(article.status) }]}>
          <Text style={styles.badgeText}>{article.status || 'pending'}</Text>
        </View>
        <View style={styles.topicBadge}>
          <Text style={styles.topicText}>{article.topic?.name || 'Uncategorized'}</Text>
        </View>
      </View>

      {/* Title */}
      <Text style={styles.title}>{article.title}</Text>

      {/* Summary */}
      <Text style={styles.summary} numberOfLines={2}>
        {article.summary}
      </Text>

      {/* Footer */}
      <View style={styles.footer}>
        <Text style={styles.metadata}>
          By {article.author?.username || 'Unknown'} | {formatDate(article.createdAt)}
        </Text>
        <View style={styles.buttonContainer}>
          <TouchableOpacity
            style={styles.readButton}
            onPress={() => onPress && onPress(article._id)}
          >
            <Text style={styles.readButtonText}>Read More</Text>
          </TouchableOpacity>

          {showEdit && onEdit && (
            <TouchableOpacity
              style={styles.editButton}
              onPress={() => onEdit(article)}
            >
              <Text style={styles.editButtonText}>Edit</Text>
            </TouchableOpacity>
          )}
        </View>
      </View>
    </TouchableOpacity>
  );
};

const styles = StyleSheet.create({
  card: {
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 16,
    marginVertical: 8,
    marginHorizontal: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  badgeContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 12,
  },
  statusBadge: {
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 12,
  },
  badgeText: {
    color: '#fff',
    fontSize: 10,
    fontWeight: '600',
    textTransform: 'capitalize',
  },
  topicBadge: {
    backgroundColor: '#e3f2fd',
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 12,
  },
  topicText: {
    color: '#1976d2',
    fontSize: 10,
    fontWeight: '500',
  },
  title: {
    fontSize: 18,
    fontWeight: '600',
    marginBottom: 8,
    color: '#1a1a1a',
  },
  summary: {
    fontSize: 14,
    color: '#666',
    marginBottom: 12,
    lineHeight: 20,
  },
  footer: {
    marginTop: 8,
  },
  metadata: {
    fontSize: 10,
    color: '#999',
    marginBottom: 12,
  },
  buttonContainer: {
    flexDirection: 'row',
    justifyContent: 'flex-end',
    gap: 8,
  },
  readButton: {
    backgroundColor: '#007AFF',
    paddingHorizontal: 16,
    paddingVertical: 6,
    borderRadius: 6,
  },
  readButtonText: {
    color: '#fff',
    fontSize: 12,
    fontWeight: '600',
  },
  editButton: {
    backgroundColor: 'transparent',
    paddingHorizontal: 16,
    paddingVertical: 6,
    borderRadius: 6,
    borderWidth: 1,
    borderColor: '#007AFF',
  },
  editButtonText: {
    color: '#007AFF',
    fontSize: 12,
    fontWeight: '600',
  },
});

export default ArticleCard;