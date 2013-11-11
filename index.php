<?php

?>
<html lang="en">
  <head>
    <meta name="viewport" content="target-densitydpi: device-dpi, width=device-width, height=device-height, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta charset="utf-8" />
    <title>Pioneer VSX Controle</title>
    <style>
      body{
        background:#000;
        overflow:hidden;
        width:700px;
        height:390px;
        padding:5px 0 0 5px;
       }
      #roomblock{
        width: 700px;
        height: 390px;
      }
    </style>
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.2.js"></script>
    <script type="text/javascript" src="jquery.mousewheel.js"></script>
    <script type="text/javascript" src="raphael-min.js"></script>
    <script type="text/javascript">
      $(function() {
        
        var scale = 1;
        
        function darkenColor(c){
          c = Raphael.color(c);
          c = Raphael.hsb2rgb(c.h,c.s,c.v*0.8);
          return c.hex;
        }
        
        var fgColor = "#0df";
        var fgDorkColor = darkenColor(fgColor);
        console.log(fgDorkColor);
        var gridSnap = true;
        var posX, posY;
        
        var rad = Math.PI / 180;
        var intEdge = Math.sqrt(18432)*scale;
        var intCenter = Math.sqrt(11520)*scale;
        
        function eDist(x1,x2,y1,y2){
          return Math.sqrt((x2-x1)*(x2-x1)+(y2-y1)*(y2-y1));
        }
        
        function animateSpeaker(xS,yS,x,y,speaker,iv){
          var d = eDist(x,xS,y,yS);
          var ip = Math.floor(iv/3);
          if(d > 2*ip){
            speaker.w3.show().attr("opacity",(d-2*ip)/(ip-2));
            speaker.w2.show().attr("opacity",1);
            speaker.w1.show().attr("opacity",1);
          }else if(d > ip){
            speaker.w3.hide();
            speaker.w2.show().attr("opacity",(d-(ip-2))/(ip-2));
            speaker.w1.show().attr("opacity",1);
          }else{
            speaker.w3.hide();
            speaker.w2.hide();
            speaker.w1.show().attr("opacity",(d+20)/(ip-2+20));
          }
        }
        
        function createSpeaker(pa,posx,posy,rot){
          var sp = new Object();
          sp.ch = pa.path("M4.998,12.127v7.896h4.495l6.729,5.526l0.004-18.948l-6.73,5.526H4.998z")
            .attr({stroke:"none",fill:fgColor})
            .translate(posx,posy).rotate(rot,15.6,15.6).scale(scale,scale,15.6,15.6);
          sp.w1 = pa.path(" M18.806,11.219c-0.393-0.389-1.024-0.389-1.415,0.002c-0.39,0.391-0.39,1.024,0.002,1.416v-0.002c0.863,0.864,1.395,2.049,1.395,3.366c0,1.316-0.531,2.497-1.393,3.361c-0.394,0.389-0.394,1.022-0.002,1.415c0.195,0.195,0.451,0.293,0.707,0.293c0.257,0,0.513-0.098,0.708-0.293c1.222-1.22,1.98-2.915,1.979-4.776C20.788,14.136,20.027,12.439,18.806,11.219z")
            .attr({stroke:"none",fill:fgColor})
            .translate(posx,posy).rotate(rot,15.6,15.6).scale(scale,scale,15.6,15.6);
          sp.w2 = pa.path("M21.101,8.925c-0.393-0.391-1.024-0.391-1.413,0c-0.392,0.391-0.392,1.025,0,1.414c1.45,1.451,2.344,3.447,2.344,5.661c0,2.212-0.894,4.207-2.342,5.659c-0.392,0.39-0.392,1.023,0,1.414c0.195,0.195,0.451,0.293,0.708,0.293c0.256,0,0.512-0.098,0.707-0.293c1.808-1.809,2.929-4.315,2.927-7.073C24.033,13.24,22.912,10.732,21.101,8.925z")
            .attr({stroke:"none",fill:fgColor,opacity:0.5})
            .translate(posx,posy).rotate(rot,15.6,15.6).scale(scale,scale,15.6,15.6);
          sp.w3 = pa.path("M23.28,6.746c-0.393-0.391-1.025-0.389-1.414,0.002c-0.391,0.389-0.391,1.023,0.002,1.413h-0.002c2.009,2.009,3.248,4.773,3.248,7.839c0,3.063-1.239,5.828-3.246,7.838c-0.391,0.39-0.391,1.023,0.002,1.415c0.194,0.194,0.45,0.291,0.706,0.291s0.513-0.098,0.708-0.293c2.363-2.366,3.831-5.643,3.829-9.251C27.115,12.389,25.647,9.111,23.28,6.746z")
            .attr({stroke:"none",fill:fgColor})
            .translate(posx,posy).rotate(rot,15.6,15.6).scale(scale,scale,15.6,15.6).hide();
          return sp;
        }
        
        var pa = Raphael("roomblock",360*scale,210*scale);

        var spL = createSpeaker(pa,15*scale,15*scale,45);
        var spR = createSpeaker(pa,175*scale,15*scale,135);
        var spC = createSpeaker(pa,95*scale,5*scale,90);
        var spSL = createSpeaker(pa,15*scale,175*scale,-45);
        var spSR = createSpeaker(pa,175*scale,175*scale,-135);
        
        var minMove = 15.6+46.4*scale;
        var maxMove = 15.6+142.4*scale;
        
        function initSpeakers(x,y){
          animateSpeaker(minMove,minMove,x,y,spL,intEdge);
          animateSpeaker(maxMove,minMove,x,y,spR,intEdge);
          animateSpeaker(15.6+94.4*scale,minMove,x,y,spC,intCenter);
          animateSpeaker(minMove,maxMove,x,y,spSL,intEdge);
          animateSpeaker(maxMove,maxMove,x,y,spSR,intEdge);
        }
       
        initSpeakers(110*scale,110*scale);
        
        //pa.rect(2,1,198,198,20)
        pa.path("M"+(15.6+202*scale)+",0L"+(15.6+202*scale)+","+(15.6+200*scale))
          .attr({fill:"rgba(0,0,0,0)",stroke:fgColor,"stroke-width":2,});
        
        var containment = pa.rect(15.6+24.4*scale,15.6+24.4*scale,140*scale,140*scale,20*scale)
          .attr({fill:"rgba(0,0,0,0)",stroke:fgColor,"stroke-width":1,"stroke-dasharray":"- "})
          .click(function(ev){
            var cx = Math.floor(ev.pageX-$("#roomblock").offset().left);
            var cy = Math.floor(ev.pageY-$("#roomblock").offset().top);
            
            cx = Math.min(Math.max((gridSnap)?cx-((cx-(15.6+.4*scale))%(2*scale)):cx,minMove),maxMove);
            cy = Math.min(Math.max((gridSnap)?cy-((cy-(15.6+.4*scale))%(2*scale)):cy,minMove),maxMove);

            moveListener(cx,cy);
          });
        
        
        var recenterIcon = pa.path("M25.083,18.895l-8.428-2.259l2.258,8.428l1.838-1.837l7.053,7.053l2.476-2.476l-7.053-7.053L25.083,18.895zM5.542,11.731l8.428,2.258l-2.258-8.428L9.874,7.398L3.196,0.72L0.72,3.196l6.678,6.678L5.542,11.731zM7.589,20.935l-6.87,6.869l2.476,2.476l6.869-6.869l1.858,1.857l2.258-8.428l-8.428,2.258L7.589,20.935zM23.412,10.064l6.867-6.87l-2.476-2.476l-6.868,6.869l-1.856-1.856l-2.258,8.428l8.428-2.259L23.412,10.064z")
          .attr({fill:fgColor,stroke:"none",opacity:.5})
          .translate(95*scale,94*scale)
          .scale(0.8*scale,0.8*scale,15.6,15.6);
        pa.circle(15.6+94.4*scale,15.6+94.4*scale,20*scale)
          .attr({fill:"rgba(0,0,0,0)",stroke:fgDorkColor,"stroke-width":1,"stroke-dasharray":"- "})
          .click(function(){
            moveListener(15.6+94.4*scale,15.6+94.4*scale);  
          })
          .mouseover(function(){
            recenterIcon.attr({opacity:1});
          })
          .mouseout(function(){
            recenterIcon.attr({opacity:0.5});
          });
        
        var listenerBG = pa.circle(15.6+94.4*scale,15.6+94.4*scale,18*scale)
          .attr({fill:"rgba(0,0,0,1)",stroke:"none"});
        var listenerIcon = pa.path("M21.021,16.349c-0.611-1.104-1.359-1.998-2.109-2.623c-0.875,0.641-1.941,1.031-3.103,1.031c-1.164,0-2.231-0.391-3.105-1.031c-0.75,0.625-1.498,1.519-2.111,2.623c-1.422,2.563-1.578,5.192-0.35,5.874c0.55,0.307,1.127,0.078,1.723-0.496c-0.105,0.582-0.166,1.213-0.166,1.873c0,2.932,1.139,5.307,2.543,5.307c0.846,0,1.265-0.865,1.466-2.189c0.201,1.324,0.62,2.189,1.463,2.189c1.406,0,2.545-2.375,2.545-5.307c0-0.66-0.061-1.291-0.168-1.873c0.598,0.574,1.174,0.803,1.725,0.496C22.602,21.541,22.443,18.912,21.021,16.349zM15.808,13.757c2.362,0,4.278-1.916,4.278-4.279s-1.916-4.279-4.278-4.279c-2.363,0-4.28,1.916-4.28,4.279S13.445,13.757,15.808,13.757z")
          .attr({fill:fgColor,stroke:"none"}).translate(94*scale,93*scale).scale(1.2*scale,1.2*scale);
        var listenerFG = pa.circle(15.6+94.4*scale,15.6+94.4*scale,18*scale)
          .attr({fill:"rgba(0,0,0,0)",stroke:fgColor,"stroke-width":2})
          .drag(
            function (dx, dy) {
              var cx = this.ox + dx;
              var cy = this.oy + dy;
              cx = Math.min(Math.max((gridSnap)?cx-((cx-(15.6+.4*scale))%(2*scale)):cx,minMove),maxMove);
              cy = Math.min(Math.max((gridSnap)?cy-((cy-(15.6+.4*scale))%(2*scale)):cy,minMove),maxMove);
              
              moveListener(cx,cy);
            },
            function () {
              this.ox = this.attr("cx");
              this.oy = this.attr("cy");
            }
          );

        function moveListener(cx,cy){
            var bx = listenerFG.attr("cx");
            var by = listenerFG.attr("cy");

            initSpeakers(cx,cy);

            listenerIcon.translate((cx-bx)/(1.2*scale),(cy-by)/(1.2*scale));
            listenerBG.attr({cx: cx,cy: cy});
            listenerFG.attr({cx: cx,cy: cy});
        }

        function Knob(pa,x,y,r,t,range,step,level,label,fgColor,handle){
          
          var allowClick = true;
          pa.circle(x,y,r).attr({stroke:fgColor,"stroke-width":2})

          var knob = pa.path();
          sector(pa.path(),x,y,r-Math.ceil(t/2)-2,-45,225).attr({stroke:fgColor,"stroke-width":t,opacity:.2});
          sector(knob,x,y,r-Math.ceil(t/2)-2,225-(270/(range[1]-range[0])*(level+(range[0] < 0?Math.abs(range[0]):0))),225)
            .attr({stroke:fgColor,"stroke-width":t,opacity:1});
            
          pa.text(x,y+(r-Math.floor((r-t)/2)),label)
            .attr({"font-family":"Arial,Helvetica,sans-serif","font-size":Math.floor((r-t)/1.5),"font-weight":900,fill:fgColor,"text-anchor":"middle",stroke:"none","fill-opacity":0.7});
          pa.text(x+Math.floor((r-t)-2*(r-2*t-(5*scale))),y-4*scale,"dB")
            .attr({"font-family":"Arial,Helvetica,sans-serif","font-size":r-2*t-5*scale,"font-weight":300,fill:fgColor,"text-anchor":"start",stroke:"none",opacity:1});

          var levelTxt = pa.text(x+Math.floor((r-t)-2*(r-2*t-5*scale)),y,(Math.floor(level)==0?"±":(level>0?"+":""))+Math.floor(level))
            .attr({"font-family":"Arial,Helvetica,sans-serif","font-size":Math.floor((r-t)/1.3),"font-weight":900,fill:fgColor,"text-anchor":"end",stroke:"none"});
          var knobFG = pa.circle(x,y,r)
            .attr({fill:"rgba(0,0,0,0)",stroke:"none"})
            .drag(
              function(dx,dy,x,y){
                dragKnob(x,y,this,changeLevel);
              },    
              function(x,y){
                allowClick = false;
                this.ophi = Math.atan((y-this.attr("cy"))/(x-this.attr("cx")))*180/Math.PI;
              },
              function(){window.setTimeout(function(){allowClick = true},200);}
            )
            .click(function(ev){
              if(!allowClick)
                return;
              var x = Math.floor(ev.pageX-$(pa.canvas.parentElement).offset().left);
              var y = Math.floor(ev.pageY-$(pa.canvas.parentElement).offset().top);
              var phi = 45 + 180 + Math.atan2(y - this.attr("cy"),x - this.attr("cx"))*180/Math.PI;
              var vol = (Math.abs(range[1]-range[0])/270*(phi%360)+(range[0]<0?range[0]:0));
              if(vol <= range[1]){
                level = vol;
                changeLevel(0);
              }
            });;
  
          $(knobFG.node).bind('mousewheel', function(event, delta) {
            changeLevel((delta > 0 ? 1 : -1));
          });
  
          function changeLevel(i){
            level = Math.min(Math.max(level-(-i*step),range[0]),range[1]);
            levelTxt.attr('text',(Math.floor(level)==0?"±":(level>0?"+":""))+Math.floor(level));
            sector(knob,x,y,r-Math.ceil(t/2)-2,225-(270/(range[1]-range[0])*(level+(range[0] < 0?Math.abs(range[0]):0))),225)
              .attr({stroke:fgColor,"stroke-width":t,opacity:1});
            handle(level);
          }

          function sector(path, cx, cy, r, startAngle, endAngle) {
            var x1 = cx + r * Math.cos(-startAngle * rad),
            x2 = cx + r * Math.cos(-endAngle * rad),
            y1 = cy + r * Math.sin(-startAngle * rad),
            y2 = cy + r * Math.sin(-endAngle * rad);
            return path.attr({path:["M", x1, y1, "A", r, r, 0, +(endAngle - startAngle > 180), 0, x2, y2]});
          }
          
          function dragKnob(x,y,dragger,handle){
            var phi = Math.atan((y-dragger.attr("cy"))/(x-dragger.attr("cx")))*180/Math.PI;
            var add = 0;
            if(phi < 0 && dragger.ophi < 0){
              if(phi < -45 && dragger.ophi > -45){
                add = -1;
                dragger.ophi = phi;
              }else if(dragger.ophi < -45 && phi > -45)
                add = 1;
                dragger.ophi = phi;
            }else if(phi > 0 && dragger.ophi > 0){
              if(phi < 45 && dragger.ophi > 45){
                add = -1;
                dragger.ophi = phi;
              }else if(phi > 45 && dragger.ophi < 45){
                add = 1;
                dragger.ophi = phi;
              }
            }else if(phi < 0 && dragger.ophi > 0){
              if(phi > -45 && dragger.ophi < 45){
                add = -1;
                dragger.ophi = phi;
              }else if(phi < -45 && dragger.ophi > 45){
                add = 1;
                dragger.ophi = phi;
              }
            }else if(phi > 0 && dragger.ophi < 0){
              if(phi > 45 && dragger.ophi < -45){
                add = -1;
                dragger.ophi = phi;
              }else if(phi < 45 && dragger.ophi > -45){
                add = 1;
                dragger.ophi = phi;
              }
            }
            if(add != 0)
              handle(add);
          }
          this.setLevel = function(l){
            level = l;
            changeLevel(0);
          }
          this.getLevel = function(){
            return level;
          }
          this.getRange = function(){
            return range;
          }
        }
        
        var bass = new Knob(pa,15.6+238*scale,136*scale,20*scale,4*scale,[-6,6],0.5,0,"Ba",fgColor,function(l){});
        var treble = new Knob(pa,15.6+282*scale,136*scale,20*scale,4*scale,[-6,6],0.5,0,"Tr",fgColor,function(l){});
        var center = new Knob(pa,15.6+238*scale,179*scale,20*scale,4*scale,[-9,9],0.5,0,"C",fgColor,function(l){});
        var subwoofer = new Knob(pa,15.6+282*scale,179*scale,20*scale,4*scale,[-6,6],0.5,0,"SW",fgColor,function(l){});
        var master = new Knob(pa,15.6+260*scale,43*scale,42*scale,15*scale,[-80,12],0.5,-60,"Vol",fgColor,function(l){});
        
        //master.callback(-50);
        
        function telVSX(){
          this.loading = false;
          this.handler = new Object();
          
          this.addHandler = function(name, handle){
            that.handler[name] = handle;
          }
          
          var emptyCount = 20;
          var that = this;
          function run(){
            var queue ="" 
            for(handle in that.handler){
              var h = that.handler[handle];
              var a = h.getArgs();
              if(a != h.lastArgs){
                queue += h.call+"("+a+")|";
                h.lastArgs = a;
              }
            }
            if(queue.length == 0 && emptyCount++ < 20){
              window.setTimeout(run,100);
              return;
            }
            console.log(queue);
            var request = $.ajax({
              url: "telVSX.php",
              type: "POST",
              data: {c : queue},
              dataType: "text",
              success: function(data){
                var ea = data.split("|");
                for (i=0;i<ea.length;i++){
                  f = ea[i].match(/(\D+)(\d+)/);
                  if(f && f.length == 3){
                    if(typeof that.handler[f[1]] == "object"){
                      that.handler[f[1]].callback(f[2]);
                    }
                  }
                }
                emptyCount = 0;
                window.setTimeout(run,500);
              }
            });

          }
          this.go = function(){
            run();
          }
        }
        
        var connection = new telVSX();
        connection.addHandler("VOL",{
          callback : function(l){
            master.setLevel(l/2-80.5);
          },
          getArgs : function(){
            return Math.round((master.getLevel()+80.5)*2);
          },
          lastArgs : Math.round((master.getLevel()+80.5)*2),
          call : "setVolume"
        });
        connection.addHandler("BALANCE",{
          callback: function(i){},
          getArgs: function(){
            var x = (listenerFG.attr("cx")-(15.6+94.4*scale))/(48*scale);
            var y = (listenerFG.attr("cy")-(15.6+94.4*scale))/(48*scale);
            var c = (center.getLevel()/(center.getRange()[1]*2));
            return ((x.toFixed(4)+","+y.toFixed(4)+","+c+",0"))
          },
          lastArgs:"0,0,0,0",
          call:"setBalance"
        });
        connection.go();
      });
    </script>
  </head>
  <body>
    <div id="roomblock"></div>
    
  </body>
</html>