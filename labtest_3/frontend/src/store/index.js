import { createStore } from 'vuex';
import axios from 'axios';

export default createStore({
  state: {
    users: []
  },
  mutations: {
    setUsers(state, users) {
      state.users = users;
    },
    addUser(state, user) {
      state.users.push(user);
    },
    updateUser(state, updatedUser) {
      const index = state.users.findIndex(user => user.id === updatedUser.id);
      if (index !== -1) {
        state.users.splice(index, 1, updatedUser);
      }
    },
    removeUser(state, userId) {
      state.users = state.users.filter(user => user.id !== userId);
    }
  },
  actions: {
    async fetchUsers({ commit }) {
      try {
        console.log('Fetching users from API...');
        const response = await axios.get('http://localhost:8000/users');
        console.log('API response:', response.data);
        commit('setUsers', response.data);
      } catch (error) {
        console.error('Error fetching users:', error);
      }
    },
    async createUser({ commit }, user) {
      const response = await axios.post('http://localhost:8000/users', user);
      commit('addUser', response.data);
    },
    async editUser({ commit }, user) {
      const response = await axios.put(`http://localhost:8000/users/${user.id}`, user);
      commit('updateUser', response.data);
    },
    async deleteUser({ commit }, userId) {
      await axios.delete(`http://localhost:8000/users/${userId}`);
      commit('removeUser', userId);
    }
  },
  getters: {
    getUsers: (state) => state.users
  }
});