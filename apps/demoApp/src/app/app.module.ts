import { NgModule, ErrorHandler } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { IonicApp, IonicModule, IonicErrorHandler } from 'ionic-angular';
import { MyApp } from './app.component';
import { HomePage } from '../pages/home/home';
import { ProjectPage } from '../pages/project/project';
import { DictionaryPage } from '../pages/dictionary/dictionary';
import { TrainingPage } from '../pages/training/training';
import { TabsControllerPage } from '../pages/tabs-controller/tabs-controller';
import { AdditionalPage } from '../pages/additional/additional';
import { ImprintPage } from '../pages/imprint/imprint';
import { TermDetailsPage } from '../pages/term-details/term-details';
import { KeywordDetailsPage } from '../pages/keyword-details/keyword-details';
import { QuestionTermPage } from '../pages/question-term/question-term';
import { AnswerTermPage } from '../pages/answer-term/answer-term';
import { AnswerSignPage } from '../pages/answer-sign/answer-sign';
import { QuestionSignPage } from '../pages/question-sign/question-sign';
import { ResetTrainingPage } from '../pages/reset-training/reset-training';
import { PrivacyPage } from '../pages/privacy/privacy';


import { StatusBar } from '@ionic-native/status-bar';
import { SplashScreen } from '@ionic-native/splash-screen';

@NgModule({
  declarations: [
    MyApp,
    HomePage,
    ProjectPage,
    DictionaryPage,
    TrainingPage,
    TabsControllerPage,
    AdditionalPage,
    ImprintPage,
    TermDetailsPage,
    KeywordDetailsPage,
    QuestionTermPage,
    AnswerTermPage,
    AnswerSignPage,
    QuestionSignPage,
    ResetTrainingPage,
    PrivacyPage
  ],
  imports: [
    BrowserModule,
    IonicModule.forRoot(MyApp)
  ],
  bootstrap: [IonicApp],
  entryComponents: [
    MyApp,
    HomePage,
    ProjectPage,
    DictionaryPage,
    TrainingPage,
    TabsControllerPage,
    AdditionalPage,
    ImprintPage,
    TermDetailsPage,
    KeywordDetailsPage,
    QuestionTermPage,
    AnswerTermPage,
    AnswerSignPage,
    QuestionSignPage,
    ResetTrainingPage,
    PrivacyPage
  ],
  providers: [
    StatusBar,
    SplashScreen,
    {provide: ErrorHandler, useClass: IonicErrorHandler}
  ]
})
export class AppModule {}