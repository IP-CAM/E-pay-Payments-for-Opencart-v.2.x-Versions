'this test for sign data 


Set k=CreateObject("KKBSign.Bean.1")

'Properties of KKBSign object - �������� ������� KKBSign

'wscript.echo(k.getKeystoretype())
'wscript.echo(k.getSignalgorythm())
'wscript.echo(k.getDebughash())

'call to signing procedure - ����� ��������� �������
b=k.build64("kkbsign.cfg","100","1")

'store signature to file - ������ ������� � ����
wscript.echo(b)
