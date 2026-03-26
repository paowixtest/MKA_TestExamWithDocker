import axios from 'axios'

axios.defaults.baseURL = '/api'

axios.interceptors.response.use(
    response => response,
    async error => {
        if (error.response && error.response.status === 401) {
            const token = localStorage.getItem('token')

            if (token) {
                try {
                    const refreshResponse = await axios.post('/auth/refresh')
                    const newToken = refreshResponse.data.access_token

                    localStorage.setItem('token', newToken)
                    axios.defaults.headers.common['Authorization'] = `Bearer ${newToken}`

                    error.config.headers['Authorization'] = `Bearer ${newToken}`
                    return axios(error.config)
                } catch (refreshError) {
                    localStorage.removeItem('token')
                    localStorage.removeItem('user')
                    window.location.href = '/login'
                }
            }
        }

        return Promise.reject(error)
    }
)

export default axios