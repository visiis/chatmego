export default {
  state: {
    userInfo: null,
    token: '',
    isLoggedIn: false
  },
  mutations: {
    SET_USER_INFO(state, userInfo) {
      state.userInfo = userInfo
    },
    SET_TOKEN(state, token) {
      state.token = token
      state.isLoggedIn = !!token
    },
    LOGOUT(state) {
      state.userInfo = null
      state.token = ''
      state.isLoggedIn = false
    }
  },
  actions: {
    setUserInfo({ commit }, userInfo) {
      commit('SET_USER_INFO', userInfo)
    },
    setToken({ commit }, token) {
      commit('SET_TOKEN', token)
    },
    logout({ commit }) {
      commit('LOGOUT')
      uni.removeStorageSync('token')
      uni.removeStorageSync('user')
    }
  },
  getters: {
    userInfo: state => state.userInfo,
    token: state => state.token,
    isLoggedIn: state => state.isLoggedIn
  }
}
