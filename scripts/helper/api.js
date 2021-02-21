/**
 * api.js
 */

'use strict';

class APIs {  
    BuildFormData(data) {
        let bodyFormData = new FormData()
        
        if (data) {
            for (let key in data) {
                bodyFormData.append(key, data[key])
            }
        }

        return bodyFormData
    }

    post(url, data, config) {    
        let bodyFormData = this.BuildFormData(data)
        return axios.post(`api/${url}`, bodyFormData, config)
    }

    get(url, data) {    
        let bodyFormData = this.BuildFormData(data)
        return axios.get(`api/${url}`, { params: bodyFormData })
    }

    delete(url, data) {    
        return axios.delete(`api/${url}`, { data: data })
    }

    put(url, data) {    
        let bodyFormData = this.BuildFormData(data)
        return axios.put(url, bodyFormData)
    }

    patch(url, data) {    
        let bodyFormData = this.BuildFormData(data)
        return axios.patch(url, bodyFormData)
    }
}

let API = new APIs()
