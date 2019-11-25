import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';
import { AnswerTermPage } from '../answer-term/answer-term';
import { TrainingPage } from '../training/training';
import { QuestionTermPage } from '../question-term/question-term';
import { AnswerSignPage } from '../answer-sign/answer-sign';
import { ResetTrainingPage } from '../reset-training/reset-training';

@Component({
  selector: 'page-question-sign',
  templateUrl: 'question-sign.html'
})
export class QuestionSignPage {

  constructor(public navCtrl: NavController) {
  }
  goToAnswerTerm(params){
    if (!params) params = {};
    this.navCtrl.push(AnswerTermPage);
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
  }
}
