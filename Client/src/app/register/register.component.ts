import { Component } from '@angular/core';
import { NavbarComponent } from "../navbar/navbar.component";
import { FormsModule } from '@angular/forms';
import { NgIf } from '@angular/common';
import { RouterLink, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [NavbarComponent, FormsModule, NgIf, RouterLink],
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']  // Corrected to styleUrls
})
export class RegisterComponent {

  formdata = { name: "", email: "", password: "" };
  submit = false;
  errorMessage = "";
  loading = false;

  constructor(private auth: AuthService, private router: Router) {}

  ngOnInit(): void {}

  onsubmit() {
    this.errorMessage = '';
    this.loading = true;
    console.log(this.formdata);

    // Call register service
    this.auth.register(this.formdata.name, this.formdata.email, this.formdata.password)
      .subscribe({
        next: data => {
          if (data.success === true) {
            console.log('Registration successful');
            this.loading = false;
            console.log(data);
            // Redirect to login page on success
            this.router.navigate(['/login']);
          } else {
            console.log('Registration failed', data);
            this.errorMessage = data.Error;
          }
        },
        error: error => {
          console.error('An error occurred:', error);
          this.errorMessage = 'An error occurred. Please try again later.';
          this.loading = false;
        }
      });
  }
}
