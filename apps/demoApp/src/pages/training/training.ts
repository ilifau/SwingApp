import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';
import { QuestionTermPage } from '../question-term/question-term';
import { AnswerSignPage } from '../answer-sign/answer-sign';
import { ResetTrainingPage } from '../reset-training/reset-training';
import { QuestionSignPage } from '../question-sign/question-sign';
import { AnswerTermPage } from '../answer-term/answer-term';

@Component({
  selector: 'page-training',
  templateUrl: 'training.html'
})
export class TrainingPage {

  constructor(public navCtrl: NavController) {
  }
  goToQuestionTerm(params){
    if (!params) params = {};
    this.navCtrl.push(QuestionTermPage);
  }goToAnswerSign(params){
    if (!params) params = {};
    this.navCtrl.push(AnswerSignPage);
  }goToTraining(params){
    if (!params) params = {};
    this.navCtrl.push(TrainingPage);
  }goToResetTraining(params){
    if (!params) params = {};
    this.navCtrl.push(ResetTrainingPage);
  }goToQuestionSign(params){
    if (!params) params = {};
    this.navCtrl.push(QuestionSignPage);
  }goToAnswerTerm(params){
    if (!params) params = {};
    this.navCtrl.push(AnswerTermPage);
  }
}
