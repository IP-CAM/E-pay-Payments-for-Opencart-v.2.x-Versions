	<%@ Language=VBScript %>
<%
  dim sum, ord
  ' ����� �� ������
  sum="0"
  '����� ������. ����� ���� ��������� ���� ������� (���������� ���� ��������). ����� ����������� �������, ��� ����
  ord="001005"
  If Request.QueryString("sum")  <> "" Then 
	sum=Request.QueryString("sum") 
  End if
  
' ������� �����
Set k= Server.CreateObject("KKBSign.Bean.1")
Base64Content=k.build64("C:\\mysite\\magazin\\kkbsign.cfg",sum,ord)

%>
<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<title>������������� ������!</title>
<body>
<h2><center>������ ������ ��������� ������</center></h2>
<h3><center>�������� - ���������</h3>
<p>
<b>����� � ������:<b>&nbsp;&nbsp;&nbsp;<%=sum%> �����
<%
'
'��� ������������, ����� ��������� �� ���� ��������� � ����������, ���� ������������ ������ ������������
'
%>
<form name="SendOrder" method="post" action="https://3dsecure.kkb.kz/jsp/process/logon.jsp">
   <input type="hidden" name="Signed_Order_B64" value="<% =Base64Content %>">
   E-mail: <input type="text" name="email" size=50 maxlength=50><p>
   <input type="hidden" name="Language" value="rus">
   <input type="hidden" name="BackLink" value="bl.asp">
   <input type="hidden" name="PostLink" value="pl.asp">
   �� ������ �������� (-�)<br>
   <input type="submit" name="GotoPay"  value="������� � ������" >&nbsp;
</form></center>
<p>

</body>
</html>
