import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';
import { DictionaryPage } from '../dictionary/dictionary';
import { TermDetailsPage } from '../term-details/term-details';
import { TrainingPage } from '../training/training';
import { QuestionTermPage } from '../question-term/question-term';
import { AnswerSignPage } from '../answer-sign/answer-sign';
import { ResetTrainingPage } from '../reset-training/reset-training';
import { QuestionSignPage } from '../question-sign/question-sign';
import { AnswerTermPage } from '../answer-term/answer-term';
import { PrivacyPage } from '../privacy/privacy';
import { ImprintPage } from '../imprint/imprint';
import { ProjectPage } from '../project/project';

@Component({
  selector: 'page-home',
  templateUrl: 'home.html'
})
export class HomePage {

  constructor(public navCtrl: NavController) {
  }
  goToDictionary(params){
    if (!params) params = {};
    this.navCtrl.push(DictionaryPage);
  }goToTermDetails(params){
    if (!params) params = {};
    this.navCtrl.push(TermDetailsPage);
  }goToTraining(params){
    if (!params) params = {};
    this.navCtrl.push(TrainingPage);
  }goToQuestionTerm(params){
    if (!params) params = {};
    this.navCtrl.push(QuestionTermPage);
  }goToAnswerSign(params){
    if (!params) params = {};
    this.navCtrl.push(AnswerSignPage);
  }goToResetTraining(params){
    if (!params) params = {};
    this.navCtrl.push(ResetTrainingPage);
  }goToQuestionSign(params){
    if (!params) params = {};
    this.navCtrl.push(QuestionSignPage);
  }goToAnswerTerm(params){
    if (!params) params = {};
    this.navCtrl.push(AnswerTermPage);
  }goToPrivacy(params){
    if (!params) params = {};
    this.navCtrl.push(PrivacyPage);
  }goToImprint(params){
    if (!params) params = {};
    this.navCtrl.push(ImprintPage);
  }goToProject(params){
    if (!params) params = {};
    this.navCtrl.push(ProjectPage);
  }
}
