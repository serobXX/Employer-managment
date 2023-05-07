import axios from "../axios/axios";

export const getEmployer = (employer_id) => {
    return axios.get(`api/users?employer_id=${employer_id}`)
}

export const destroyEmployer = (employer_id) => {
    return axios.delete(`api/users/${employer_id}`)
}

export const storeEmployer=(data)=>{
    return axios.post('api/users',{...data})
}
export const updateEmployer=({data,id})=>{
    console.log(6666,data);
    return axios.patch(`api/users/${id}`,{...data})
}