import { Component,OnInit } from '@angular/core';
import { NavbarComponent } from "../navbar/navbar.component";
import { FormsModule } from '@angular/forms';
import { NgIf } from '@angular/common';
import { RouterLink } from '@angular/router';
import { AuthService } from '../services/auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [NavbarComponent,FormsModule,NgIf,RouterLink],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent  implements OnInit {
  formdata = { email: "", password: "" }
  submit = false;
  errorMessage = "";
  loading = false;

 constructor(private auth:AuthService) {}

 ngOnInit(): void {
   }

 onSubmit() {
  this.errorMessage='';
  this.loading=true;
  console.log(this.formdata)
 

 //call login service
 this.auth.login( this.formdata.email, this.formdata.password)
 .subscribe({
   next: data => {
     //store stoken from response data
     if (data.success === true) {
       console.log('Login successful');
      this.loading = false;
      sessionStorage.setItem("token",data.authToken);
      sessionStorage.setItem("hash",data.hash);
      sessionStorage.setItem("name",data.name);
   console.log(data);
   this.auth.canAuthenticate();
     } else {
       console.log('Login failed',data);
       this.errorMessage = data.Error;
     }
   
 },
 error: error => {
   console.error('An error occurred:', error);
     this.errorMessage = 'An error occurred. Please try again later.';
 }
});
}
}
