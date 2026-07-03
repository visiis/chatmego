export default {
  state: {
    chats: [],
    messages: [],
    currentFriendId: null,
    unreadCount: 0
  },
  mutations: {
    SET_CHATS(state, chats) {
      state.chats = chats
    },
    SET_MESSAGES(state, messages) {
      state.messages = messages
    },
    ADD_MESSAGE(state, message) {
      state.messages.push(message)
    },
    SET_CURRENT_FRIEND_ID(state, friendId) {
      state.currentFriendId = friendId
    },
    SET_UNREAD_COUNT(state, count) {
      state.unreadCount = count
    },
    UPDATE_CHAT_LAST_MESSAGE(state, { friendId, lastMessage }) {
      const chat = state.chats.find(c => c.friend.id === friendId)
      if (chat) {
        chat.last_message = lastMessage
        chat.unread_count = (chat.unread_count || 0) + 1
      }
    },
    CLEAR_UNREAD(state, friendId) {
      const chat = state.chats.find(c => c.friend.id === friendId)
      if (chat) {
        chat.unread_count = 0
      }
    }
  },
  actions: {
    setChats({ commit }, chats) {
      commit('SET_CHATS', chats)
    },
    setMessages({ commit }, messages) {
      commit('SET_MESSAGES', messages)
    },
    addMessage({ commit }, message) {
      commit('ADD_MESSAGE', message)
    },
    setCurrentFriendId({ commit }, friendId) {
      commit('SET_CURRENT_FRIEND_ID', friendId)
    },
    setUnreadCount({ commit }, count) {
      commit('SET_UNREAD_COUNT', count)
    },
    updateChatLastMessage({ commit }, payload) {
      commit('UPDATE_CHAT_LAST_MESSAGE', payload)
    },
    clearUnread({ commit }, friendId) {
      commit('CLEAR_UNREAD', friendId)
    }
  },
  getters: {
    chats: state => state.chats,
    messages: state => state.messages,
    currentFriendId: state => state.currentFriendId,
    unreadCount: state => state.unreadCount
  }
}
