import axios from 'axios';

const HOST = "http://localhost:8080";
let headers = { "Content-Type" : "application/json" };

const status401 = () => { 
  clearUser();
  window.location.href = '/login';  
}

const catchError = (error,reject) => {
  if( error.response ){
    if( error.response.status === 401 ) {
      status401(error);
    }
    reject({ message: error.response.data.message });
  }
  reject({ message: 'Oops, tivemos um problema de comunicação com o servidor, tente novamente!' });
} 

const setTokenHeaders = (token) => {
  headers['Authorization'] = `bearer ${token}`;
}

export const getUser = () => {
  const user = localStorage.getItem('userLogged');
  if( !user || user === 'null' || user === 'undefined' ) {
    return null;
  }
  const userObj = JSON.parse(user);
  return userObj;
}

export const setUser = (userParam) => {
  try{
    const user = JSON.stringify({
      email : userParam.email,
      token : userParam.token
    });  
    localStorage.setItem('userLogged',user);
    return true;
  }catch(error){
    return false;
  }
}

export const clearUser = () => {
  localStorage.removeItem('userLogged');
}

export const apiGet = async (endpoint, params) => {
  return new Promise((resolve,reject) => {
    
    const user = getUser();

    if( user ) {

      setTokenHeaders(user.token);

      axios.get(`${HOST}${endpoint}${params}`,{
        headers
      }).then((response) =>{
        if( response.status === 200 ){
          resolve(response.data);
        }else{
          catchError(response,reject);
        }
      }).catch((error) => catchError(error,reject));  
    }

  });
}

export const apiPost = async (endpoint, params, notAuth = false) => {
  return new Promise((resolve,reject) => {
    
    const user = getUser();

    if( user || notAuth ) {

      if( user ) {
        setTokenHeaders(user.token);
      }

      axios.post(`${HOST}${endpoint}`,params,{
        headers
      }).then((response) =>{
        if( response.status === 200 ){
          resolve(response.data);
        }else{
          catchError(response,reject);
        }
      }).catch((error) => {
        status401(error);
        reject(error);
      });  
    }

  });
}

export const apiPut = async (endpoint, params) => {
  return new Promise((resolve,reject) => {
    
    const user = getUser();

    if( user ) {

      setTokenHeaders(user.token);

      axios.put(`${HOST}${endpoint}`,params,{
        headers
      }).then((response) =>{
        if( response.status === 200 ){
          resolve(response.data);
        }else{
          catchError(response,reject);
        }
      }).catch((error) => {
        status401(error);
        reject(error);
      });  
    }

  });
}

export const apiDelete = async (endpoint) => {
  return new Promise((resolve,reject) => {
    
    const user = getUser();

    if( user ) {

      setTokenHeaders(user.token);
      
      axios.delete(`${HOST}${endpoint}`,{
        headers
      }).then((response) =>{
        if( response.status === 200 ){
          resolve(response.data);
        }else{
          catchError(response,reject);
        }
      }).catch((error) => {
        status401(error);
        reject(error);
      });  
    }

  });
}

