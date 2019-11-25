import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';
import { TrainingPage } from '../training/training';
import { QuestionTermPage } from '../question-term/question-term';
import { ResetTrainingPage } from '../reset-training/reset-training';
import { QuestionSignPage } from '../question-sign/question-sign';
import { AnswerTermPage } from '../answer-term/answer-term';

@Component({
  selector: 'page-answer-sign',
  templateUrl: 'answer-sign.html'
})
export class AnswerSignPage {

  constructor(public navCtrl: NavController) {
  }
  goToTraining(params){
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
  }
}
