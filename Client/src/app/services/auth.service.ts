import { Inject, Injectable, PLATFORM_ID } from '@angular/core';
import { isPlatformBrowser } from '@angular/common';
import { Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  constructor(
    private router: Router, 
    private http: HttpClient,
    @Inject(PLATFORM_ID) private platformId: Object // Inject PLATFORM_ID
  ) {}

  isAuthenticated(): boolean {
    if (isPlatformBrowser(this.platformId)) { // Check if platform is browser
      return sessionStorage.getItem('token') !== null;
    }
    return false;
  }

  canAccess() {
    if (!this.isAuthenticated()) {   
      // Redirect to login if not authenticated
      this.router.navigate(['/login']);
    }
  }

  canAuthenticate(){
    if (this.isAuthenticated()) {
      // Redirect to profile if authenticated
      this.router.navigate(['/profile']);
    }
  }

  register(name: string, email: string, password: string): Observable<any> {
    const headers = { 'Content-Type': 'application/json' };
    const body = JSON.stringify({ name, email, password });
    return this.http.post('http://localhost/Login/Server/src/php/register.php', body, { headers });
  }

  storeToken(token: string) {
    if (isPlatformBrowser(this.platformId)) { // Ensure sessionStorage is only accessed in the browser
      sessionStorage.setItem('token', token);
    }
  }

  login(email: string, password: string): Observable<any> {
    const headers = { 'Content-Type': 'application/json' };
    const body = JSON.stringify({ email, password });
    return this.http.post('http://localhost/Login/Server/src/php/login.php', body, { headers });
  }

  updateprofile(dob: string, age: number, contact: string): Observable<any> {
    const token = isPlatformBrowser(this.platformId) ? sessionStorage.getItem('token') || '' : '';
    const hash = isPlatformBrowser(this.platformId) ? sessionStorage.getItem('hash') || '' : '';

    const headers = {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    };

    const body = JSON.stringify({ dob, age, contact, hash });
    console.log("hash",hash);
    console.log(",token",token);
    
    

    return this.http.post('http://localhost/Login/Server/src/php/update_profile.php', body, { headers });
  }

  // New method to get profile data, sending hash in the request body
  getProfile(): Observable<any> {
    const token = isPlatformBrowser(this.platformId) ? sessionStorage.getItem('token') || '' : '';
    const hash = isPlatformBrowser(this.platformId) ? sessionStorage.getItem('hash') || '' : '';

    const headers = {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    };
    const body = { hash:hash }; // Retrieve hash from localStorage
    return this.http.post('http://localhost/Login/Server/src/php/profile.php', body, { headers });
  }
}
