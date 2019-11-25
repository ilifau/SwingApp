import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';
import { TermDetailsPage } from '../term-details/term-details';

@Component({
  selector: 'page-dictionary',
  templateUrl: 'dictionary.html'
})
export class DictionaryPage {

  constructor(public navCtrl: NavController) {
  }
  goToTermDetails(params){
    if (!params) params = {};
    this.navCtrl.push(TermDetailsPage);
  }
}
