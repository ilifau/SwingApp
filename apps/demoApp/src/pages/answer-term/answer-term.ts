import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';
import { TrainingPage } from '../training/training';
import { QuestionTermPage } from '../question-term/question-term';
import { AnswerSignPage } from '../answer-sign/answer-sign';
import { ResetTrainingPage } from '../reset-training/reset-training';
import { QuestionSignPage } from '../question-sign/question-sign';

@Component({
  selector: 'page-answer-term',
  templateUrl: 'answer-term.html'
})
export class AnswerTermPage {

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
