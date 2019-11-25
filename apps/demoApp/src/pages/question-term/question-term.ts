import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';
import { AnswerSignPage } from '../answer-sign/answer-sign';
import { TrainingPage } from '../training/training';
import { ResetTrainingPage } from '../reset-training/reset-training';
import { QuestionSignPage } from '../question-sign/question-sign';
import { AnswerTermPage } from '../answer-term/answer-term';

@Component({
  selector: 'page-question-term',
  templateUrl: 'question-term.html'
})
export class QuestionTermPage {

  constructor(public navCtrl: NavController) {
  }
  goToAnswerSign(params){
    if (!params) params = {};
    this.navCtrl.push(AnswerSignPage);
  }goToTraining(params){
    if (!params) params = {};
    this.navCtrl.push(TrainingPage);
  }goToQuestionTerm(params){
    if (!params) params = {};
    this.navCtrl.push(QuestionTermPage);
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
