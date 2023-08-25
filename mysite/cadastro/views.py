from django.shortcuts import render
from django.http import HttpResponse

def index(request):
    contexto = {"year": 2023}
    return render(request, 'index.html', contexto)
    #return HttpResponse("Hello, world Cadastro.")
