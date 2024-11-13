import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import {RouterLink } from '@angular/router';
import { AuthService } from '../services/auth.service';
import { Router } from '@angular/router';


@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [RouterLink,CommonModule],
  templateUrl: './navbar.component.html',
  styleUrl: './navbar.component.css'
})
export class NavbarComponent implements OnInit {
  
  constructor(public auth:AuthService, private router: Router ){}
  
  ngOnInit(): void {
    
  }

  logout() {
    // Remove the token and other relevant user data from sessionStorage
    sessionStorage.removeItem('token');
    sessionStorage.removeItem('hash');

    // Redirect to login page or home
    this.router.navigate(['/login']);
  }
}
