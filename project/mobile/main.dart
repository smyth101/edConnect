import 'dart:async';

import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';

void main() {
  runApp(EdconnectApp());
}

Map<int, Color> color =
{
  50:Color.fromRGBO(164,227,245, .1),
  100:Color.fromRGBO(164,227,245, .2),
  200:Color.fromRGBO(164,227,245, .3),
  300:Color.fromRGBO(164,227,245, .4),
  400:Color.fromRGBO(164,227,245, .5),
  500:Color.fromRGBO(164,227,245, .6),
  600:Color.fromRGBO(164,227,245, .7),
  700:Color.fromRGBO(164,227,245, .8),
  800:Color.fromRGBO(164,227,245, .9),
  900:Color.fromRGBO(164,227,245, 1),
};
MaterialColor colorCustom = MaterialColor(0xFF89D3FB, color);

class EdconnectApp extends StatelessWidget {
  // This widget is the root of your application.
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'edconnect',
      theme: ThemeData(

        primarySwatch: colorCustom,

        visualDensity: VisualDensity.adaptivePlatformDensity,
      ),
      home: EdconnectHomePage(),
    );
  }
}

class EdconnectHomePage extends StatefulWidget {
  EdconnectHomePage({Key key, this.title}) : super(key: key);

  final String title;

  @override
  _EdconnectHomePageState createState() => _EdconnectHomePageState();
}

class _EdconnectHomePageState extends State<EdconnectHomePage> {
  final Completer<WebViewController> _controller = Completer<WebViewController>();
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: PreferredSize(
        preferredSize: Size.fromHeight(0.0),
    child:AppBar(
      )
      ),
      body: WebView(
        initialUrl: "https://edconnect.ie",
        javascriptMode: JavascriptMode.unrestricted,
        onWebViewCreated: (WebViewController webViewController){
          _controller.complete(webViewController);
        },
      )

    );
  }
}
