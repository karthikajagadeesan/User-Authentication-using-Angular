import { Component } from '@angular/core';
import { NavbarComponent } from "../navbar/navbar.component";
import { AuthService } from '../services/auth.service';
import { FormsModule } from '@angular/forms';
import { NgIf } from '@angular/common';
import { Router } from '@angular/router';

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [NavbarComponent, FormsModule, NgIf],
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css'] // Fixed typo: should be styleUrls, not styleUrl
})
export class ProfileComponent {

  formdata ={ dob: "", age: 0, contactnumber: "" };
  submit = false;
  errorMessage = "";
  loading = false;

  constructor(private auth: AuthService, private router: Router) {}

  ngOnInit() {
    // Check if user is authenticated
    if (!this.auth.isAuthenticated()) {
      this.router.navigate(['/login']); // Redirect to login if not authenticated
      return;
    }

    this.loading = true;
    this.auth.getProfile().subscribe({
      next: (data) => {
        if (data && data.success) {
          // Populate formdata with received profile details
          this.formdata.dob = data.profile.dob || "";
          this.formdata.age = data.profile.age || 0;
          this.formdata.contactnumber = data.profile.contact || "";
        }
        this.loading = false;
      },
      error: (error) => {
        console.error('Failed to load profile data:', error);
        this.errorMessage = 'Unable to load profile data. Please try again later.';
        this.loading = false;
      }
    });
  }


  onsubmit() {
    this.errorMessage = '';
    this.loading = true;
    console.log(this.formdata);

    // Call profile service
    this.auth.updateprofile(this.formdata.dob, this.formdata.age, this.formdata.contactnumber)
      .subscribe({
        next: data => {
          if (data.success === true) {
            console.log('Update profile successful');
            this.loading = false;
            console.log(data);
          } else {
            console.log('Update profile failed', data);
            this.errorMessage = data.Error;
            this.loading = false; // Stop loading if failed
          }
        },
        error: error => {
          console.error('An error occurred:', error);
          this.errorMessage = 'An error occurred. Please try again later.';
          this.loading = false; // Stop loading on error
        }
      });
  }
}
