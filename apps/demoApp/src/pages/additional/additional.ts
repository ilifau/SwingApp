import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';
import { KeywordDetailsPage } from '../keyword-details/keyword-details';

@Component({
  selector: 'page-additional',
  templateUrl: 'additional.html'
})
export class AdditionalPage {

  constructor(public navCtrl: NavController) {
  }
  goToKeywordDetails(params){
    if (!params) params = {};
    this.navCtrl.push(KeywordDetailsPage);
  }
}
