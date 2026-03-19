import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        auth:false,
        admin:false
    }),
    //Getter Methode
    getters: {
        isLoggedIn: (state) => !!state.user,
        authState: (state) => !!state.auth,
        adminState: (state) => !!state.admin,
    },

    actions: {
        setUser(user) {
            this.user = user
        },

        clearUser() {
            this.user = null
        },

        async fetchUser() {
            try {
                const response = await fetch('/me', {
                    headers: {
                        'Accept': 'application/json'
                    }
                })

                if (!response.ok) {
                    this.user = null
                    return
                }

                const data = await response.json()
                this.user = data
                this.authState = true;
                if(data.role === 1) {
                   this.admin = true;
                }

            } catch (error) {
                this.user = null
                console.error(error)
            }
        },

        async logout() {
            try {
                await fetch('/logout', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute('content')
                    }
                })
            } catch (error) {
                console.error(error)
            }

            this.user = null
        }
    }
})
