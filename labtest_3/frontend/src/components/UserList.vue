<template>
  <div class="container-fluid">
    <div class="row">
      <!-- Add/Edit User Form -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <h2 class="card-title">{{ isEditing ? 'Edit User' : 'Add New User' }}</h2>
            <form @submit.prevent="submitForm" class="needs-validation" novalidate>
              <div class="form-group mb-3">
                <input v-model="form.name" placeholder="Name" :class="{ 'is-invalid': nameError }" class="form-control" required>
                <div v-if="nameError" class="invalid-feedback">Name is required.</div>
              </div>
              
              <div class="form-group mb-4">
                <input v-model="form.email" placeholder="Email" :class="{ 'is-invalid': emailError }" class="form-control" required>
                <div v-if="emailError" class="invalid-feedback">Email is required and must be valid.</div>
              </div>
              
              <button type="submit" class="btn btn-primary me-2">
                {{ isEditing ? 'Update User' : 'Add User' }}
              </button>
              <button v-if="isEditing" @click="cancelEdit" type="button" class="btn btn-secondary">
                Cancel
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- User List -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <h1 class="card-title">Users</h1>
            <table class="table">
              <thead class="thead-light">
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users" :key="user.id">
                  <td>{{ user.name }}</td>
                  <td>{{ user.email }}</td>
                  <td>
                    <button @click="startEdit(user)" class="btn btn-primary btn-sm me-1">Edit</button>
                    <button @click="removeUser(user.id)" class="btn btn-danger btn-sm">Remove</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useStore } from 'vuex';

export default {
  setup() {
    const store = useStore();
    const form = ref({
      id: null,
      name: '',
      email: ''
    });

    const users = computed(() => store.state.users);
    const isEditing = ref(false);
    const nameError = ref(false);
    const emailError = ref(false);

    const validateEmail = (email) => {
      const re = /\S+@\S+\.\S+/;
      return re.test(email);
    };

    const submitForm = () => {
      nameError.value = form.value.name === '';
      emailError.value = form.value.email === '' || !validateEmail(form.value.email);

      if (nameError.value || emailError.value) {
        return;
      }

      if (isEditing.value) {
        store.dispatch('editUser', form.value).then(() => {
          store.dispatch('fetchUsers'); // Fetch updated list after editing
        });
      } else {
        store.dispatch('createUser', form.value).then(() => {
          store.dispatch('fetchUsers'); // Fetch updated list after adding
        });
      }

      resetForm();
    };

    const removeUser = (userId) => {
      if (confirm('Are you sure you want to remove this user?')) {
        store.dispatch('deleteUser', userId).then(() => {
          store.dispatch('fetchUsers'); // Fetch updated list after removal
        });
      }
    };

    const startEdit = (user) => {
      form.value = { ...user };
      isEditing.value = true;
    };

    const cancelEdit = () => {
      resetForm();
    };

    const resetForm = () => {
      form.value = { id: null, name: '', email: '' };
      isEditing.value = false;
      nameError.value = false;
      emailError.value = false;
    };

    onMounted(() => {
      store.dispatch('fetchUsers');
    });

    return {
      form,
      users,
      isEditing,
      submitForm,
      startEdit,
      cancelEdit,
      removeUser,
      nameError,
      emailError
    };
  }
};
</script>
