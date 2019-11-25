import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';
import { AdditionalPage } from '../additional/additional';
import { KeywordDetailsPage } from '../keyword-details/keyword-details';
import { HomePage } from '../home/home';
import { DictionaryPage } from '../dictionary/dictionary';
import { TrainingPage } from '../training/training';

@Component({
  selector: 'page-tabs-controller',
  templateUrl: 'tabs-controller.html'
})
export class TabsControllerPage {

  tab1Root: any = HomePage;
  tab2Root: any = DictionaryPage;
  tab3Root: any = TrainingPage;
  tab4Root: any = AdditionalPage;
  constructor(public navCtrl: NavController) {
  }
  goToAdditional(params){
    if (!params) params = {};
    this.navCtrl.push(AdditionalPage);
  }goToKeywordDetails(params){
    if (!params) params = {};
    this.navCtrl.push(KeywordDetailsPage);
  }
}
